with open('assets/css/jit-offcanvas.css', 'r', encoding='utf-8') as f:
    text = f.read()

# Add / ensure exact offcanvas styling rules matching Image 1
patch = """
/* ---------------------------------------------------- */
/* Offcanvas Typography & Polish (Exact Match to Home)  */
/* ---------------------------------------------------- */
.offcanvas__menu-wrapper.mean-container .mean-nav ul li a {
  text-transform: uppercase !important;
  font-family: 'Kanit', sans-serif !important;
  font-weight: 500 !important;
  font-size: clamp(32px, 4.2vw, 68px) !important;
  line-height: 1.15 !important;
  color: #ffffff !important;
  padding: 16px 0 16px clamp(20px, 4vw, 80px) !important;
  border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
  display: flex !important;
  align-items: center !important;
  letter-spacing: 0.5px !important;
}

.offcanvas__menu-wrapper.mean-container .mean-nav ul li a:hover {
  color: var(--primary, #c9f31d) !important;
}

.offcanvas__menu-wrapper.mean-container .mean-nav ul li:last-child a {
  border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.offcanvas__contact h3 {
  font-size: 24px !important;
  font-weight: 500 !important;
  color: #ffffff !important;
  margin-bottom: 22px !important;
}

.offcanvas__contact ul {
  list-style: none !important;
  padding: 0 !important;
  margin: 0 !important;
}

.offcanvas__contact ul li {
  margin-bottom: 14px !important;
}

.offcanvas__contact ul li a {
  color: #ffffff !important;
  font-size: 16px !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 10px !important;
  text-decoration: none !important;
  transition: color 0.2s ease !important;
}

.offcanvas__contact ul li a:hover {
  color: var(--primary, #c9f31d) !important;
}

.offcanvas__mid::-webkit-scrollbar {
  width: 5px !important;
}

.offcanvas__mid::-webkit-scrollbar-track {
  background: #111210 !important;
}

.offcanvas__mid::-webkit-scrollbar-thumb {
  background: var(--primary, #c9f31d) !important;
  border-radius: 10px !important;
}
"""

if '/* Offcanvas Typography & Polish' not in text:
    text = text.rstrip() + '\n\n' + patch.strip() + '\n'

with open('assets/css/jit-offcanvas.css', 'w', encoding='utf-8') as f:
    f.write(text)

print("SUCCESS: Updated assets/css/jit-offcanvas.css with Home/About matching offcanvas styles")

