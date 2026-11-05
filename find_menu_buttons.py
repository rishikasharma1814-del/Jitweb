with open('business-coaching.html', 'r', encoding='utf-8') as f:
    text = f.read()

import re
# Find all buttons in business-coaching.html that might be a menu button
buttons = re.findall(r'<button[^>]*>', text)
for b in buttons:
    if any(k in b.lower() for k in ['menu', 'offcanvas', 'open', 'nav']):
        print(b)

