with open('index.html', 'r', encoding='utf-8') as f:
    index_html = f.read()

import re
m = re.search(r'<div class="offcanvas__area">.*?<!-- Offcanvas area end -->', index_html, re.DOTALL)
if not m:
    # try without comment
    m = re.search(r'<div class="offcanvas__area">.*?</div>\s*</div>\s*</div>', index_html, re.DOTALL)

print("Found offcanvas in index.html, length:", len(m.group(0)) if m else 0)
exact_offcanvas = m.group(0)

with open('business-coaching.html', 'r', encoding='utf-8') as f:
    bc_html = f.read()

m_bc = re.search(r'<div class="offcanvas__area">.*?<!-- Offcanvas area end -->', bc_html, re.DOTALL)
if not m_bc:
    m_bc = re.search(r'<div class="offcanvas__area">.*?</div>\s*</div>\s*</div>', bc_html, re.DOTALL)

print("Found offcanvas in business-coaching.html, length:", len(m_bc.group(0)) if m_bc else 0)

# Replace in business-coaching.html
bc_html = bc_html[:m_bc.start()] + exact_offcanvas + bc_html[m_bc.end():]

with open('business-coaching.html', 'w', encoding='utf-8') as f:
    f.write(bc_html)

print("SUCCESS: Replaced offcanvas in business-coaching.html with exact offcanvas from index.html!")

