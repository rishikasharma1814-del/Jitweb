/**
 * Vercel Serverless: browser → same-origin /api/contact → InfinityFree PHP (no CORS in browser).
 * Optional: set RESEND_API_KEY + CONTACT_TO_EMAIL in Vercel env if upstream returns HTML bot-wall.
 */

const UPSTREAM_URL = "https://jitapi.infinityfree.me/send-mail.php";

const BROWSER_HEADERS = {
  "User-Agent":
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
  Accept: "application/json, text/plain, */*",
  "Accept-Language": "en-US,en;q=0.9",
};

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

function getPayload(req) {
  const b = req.body;
  if (!b) {
    return { name: "", email: "", phone: "", subject: "", message: "" };
  }
  if (typeof b === "string") {
    const p = new URLSearchParams(b);
    return {
      name: p.get("name") || "",
      email: p.get("email") || "",
      phone: p.get("phone") || "",
      subject: p.get("subject") || "",
      message: p.get("message") || "",
    };
  }
  return {
    name: String(b.name ?? ""),
    email: String(b.email ?? ""),
    phone: String(b.phone ?? ""),
    subject: String(b.subject ?? ""),
    message: String(b.message ?? ""),
  };
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
      Origin: "https://jitapi.infinityfree.me",
      Referer: "https://jitapi.infinityfree.me/",
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
  if (!res.ok) return { ok: false, detail: data };
  return { ok: true, data };
}

async function handler(req, res) {
  res.setHeader("Content-Type", "application/json");
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "POST, OPTIONS");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Requested-With");

  if (req.method === "OPTIONS") {
    return res.status(204).end();
  }

  if (req.method !== "POST") {
    return res.status(405).json({ status: "error", message: "Method not allowed" });
  }

  try {
    const payload = getPayload(req);
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
        return res.status(upstream.ok ? 200 : 502).json(data);
      } catch {
        /* fall through */
      }
    }

    const resend = await sendViaResend(payload);
    if (resend && resend.ok) {
      return res.status(200).json({
        status: "success",
        message:
          "Thank you — we received your message. Our team will get back to you soon.",
        via: "resend",
      });
    }

    return res.status(502).json({
      status: "error",
      message:
        "Contact API could not reach your PHP host (HTML security page instead of JSON). Add RESEND_API_KEY in Vercel env, or use PHP hosting that allows server requests.",
      hint: "Vercel → InfinityFree proxy blocked or invalid response",
      upstream_status: upstream.status,
      resend_error: resend && !resend.ok ? resend.detail : undefined,
    });
  } catch (e) {
    return res.status(500).json({
      status: "error",
      message: "Server error",
      detail: e.message || "Unknown",
    });
  }
}

module.exports = handler;
