/**
 * Vercel Serverless: browser → /api/contact → Resend (transactional email API).
 * Requires RESEND_API_KEY in Vercel env (Project → Settings → Environment Variables).
 * Optional: CONTACT_TO_EMAIL (default info@jewarinternational.com), CONTACT_FROM_EMAIL.
 */

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

async function sendViaResend(payload) {
  const key = process.env.RESEND_API_KEY;
  if (!key) {
    return { ok: false, detail: "RESEND_API_KEY is not configured" };
  }

  const to = process.env.CONTACT_TO_EMAIL || "info@jewarinternational.com";
  const senderAddress = process.env.CONTACT_FROM_EMAIL || "onboarding@resend.dev";
  const from = `${payload.name} via JIT Website <${senderAddress}>`;

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

    if (!payload.name || !payload.email || !payload.message) {
      return res.status(400).json({
        status: "error",
        message: "Name, email, and message are required",
      });
    }

    const resend = await sendViaResend(payload);
    if (resend.ok) {
      return res.status(200).json({
        status: "success",
        message: "Thank you — we received your message. Our team will get back to you soon.",
      });
    }

    return res.status(502).json({
      status: "error",
      message: "Could not send your message right now. Please email info@jewarinternational.com directly.",
      resend_error: resend.detail,
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
