const UPSTREAM_ORIGIN = "https://jitapi.infinityfree.me";
const UPSTREAM_URL = `${UPSTREAM_ORIGIN}/send-mail.php`;

const BROWSER_HEADERS = {
  "User-Agent":
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
  Accept: "application/json, text/plain, */*",
  "Accept-Language": "en-US,en;q=0.9",
};

function responseHeaders() {
  return {
    "Content-Type": "application/json",
    "Access-Control-Allow-Origin": "*",
    "Access-Control-Allow-Methods": "POST, OPTIONS",
    "Access-Control-Allow-Headers": "Content-Type, Authorization, X-Requested-With",
  };
}

/** InfinityFree often returns an HTML "browser check" page to non-browser / datacenter clients. */
function looksLikeHostBotWall(text) {
  const t = (text || "").trim();
  if (!t) return true;
  const lower = t.slice(0, 2000).toLowerCase();
  return (
    lower.startsWith("<!doctype") ||
    lower.startsWith("<html") ||
    lower.includes("aes.js") ||
    lower.includes("function tonumbers(")
  );
}

function cookieHeaderFromResponse(res) {
  const h = res.headers;
  if (typeof h.getSetCookie === "function") {
    const parts = h.getSetCookie();
    if (!parts || !parts.length) return "";
    return parts.map((c) => c.split(";")[0].trim()).filter(Boolean).join("; ");
  }
  const single = h.get("set-cookie");
  return single ? single.split(";")[0].trim() : "";
}

async function postToInfinityFree(formBody) {
  const warm = await fetch(`${UPSTREAM_URL}?ping=1`, {
    method: "GET",
    headers: { ...BROWSER_HEADERS },
  });
  const cookie = cookieHeaderFromResponse(warm);

  return fetch(UPSTREAM_URL, {
    method: "POST",
    headers: {
      ...BROWSER_HEADERS,
      "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
      Origin: UPSTREAM_ORIGIN,
      Referer: `${UPSTREAM_ORIGIN}/`,
      ...(cookie ? { Cookie: cookie } : {}),
    },
    body: formBody,
  });
}

async function sendViaResend(payload) {
  const key = process.env.RESEND_API_KEY;
  if (!key) return null;

  const to = process.env.CONTACT_TO_EMAIL || "info@jewarinternational.com";
  const from =
    process.env.CONTACT_FROM_EMAIL || "JIT Contact Form <onboarding@resend.dev>";

  const res = await fetch("https://api.resend.com/emails", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${key}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      from,
      to: [to],
      reply_to: payload.email,
      subject: `[Website contact] ${payload.subject || "(no subject)"}`,
      text: [
        `Name: ${payload.name}`,
        `Email: ${payload.email}`,
        `Phone: ${payload.phone || "-"}`,
        "",
        payload.message || "",
      ].join("\n"),
    }),
  });

  const raw = await res.text();
  let data;
  try {
    data = JSON.parse(raw);
  } catch {
    data = { raw: raw.slice(0, 300) };
  }

  if (!res.ok) {
    return { ok: false, detail: data };
  }
  return { ok: true, data };
}

exports.handler = async (event) => {
  const headers = responseHeaders();

  if (event.httpMethod === "OPTIONS") {
    return { statusCode: 200, headers, body: JSON.stringify({ ok: true }) };
  }

  if (event.httpMethod !== "POST") {
    return {
      statusCode: 405,
      headers,
      body: JSON.stringify({ status: "error", message: "Method not allowed" }),
    };
  }

  try {
    const contentType = (
      event.headers["content-type"] ||
      event.headers["Content-Type"] ||
      ""
    ).toLowerCase();
    let payload = {};

    if (contentType.includes("application/json")) {
      payload = JSON.parse(event.body || "{}");
    } else {
      const params = new URLSearchParams(event.body || "");
      payload = {
        name: params.get("name") || "",
        email: params.get("email") || "",
        phone: params.get("phone") || "",
        subject: params.get("subject") || "",
        message: params.get("message") || "",
      };
    }

    const form = new URLSearchParams();
    form.set("name", payload.name || "");
    form.set("email", payload.email || "");
    form.set("phone", payload.phone || "");
    form.set("subject", payload.subject || "");
    form.set("message", payload.message || "");
    const formBody = form.toString();

    const upstream = await postToInfinityFree(formBody);
    const raw = await upstream.text();

    if (!looksLikeHostBotWall(raw)) {
      try {
        const data = JSON.parse(raw);
        return {
          statusCode: upstream.ok ? 200 : 502,
          headers,
          body: JSON.stringify(data),
        };
      } catch {
        /* fall through to fallback */
      }
    }

    const resend = await sendViaResend(payload);
    if (resend && resend.ok) {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          status: "success",
          message:
            "Thank you — we received your message. Our team will get back to you soon.",
          via: "resend",
        }),
      };
    }

    return {
      statusCode: 502,
      headers,
      body: JSON.stringify({
        status: "error",
        message:
          "Contact service is temporarily unavailable. InfinityFree is blocking automated server requests from Netlify (browser-check page instead of API).",
        hint:
          "Fix options: (1) In Netlify → Site settings → Environment variables, add RESEND_API_KEY (and optional CONTACT_TO_EMAIL); redeploy. (2) Or host the PHP API on a provider that allows server-to-server calls without this wall.",
        upstream_status: upstream.status,
        resend_error: resend && !resend.ok ? resend.detail : undefined,
      }),
    };
  } catch (error) {
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({
        status: "error",
        message: "Netlify function request failed",
        detail: error.message || "Unknown error",
      }),
    };
  }
};
