with open('business-coaching.html', 'r', encoding='utf-8') as f:
    text = f.read()

import re
matches = [m.start() for m in re.finditer(r'j26-nine|menu-white|open_offcanvas', text)]
for idx in matches:
    print(text[max(0, idx-60):min(len(text), idx+100)].replace('\n', ' '))

