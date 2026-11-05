with open('assets/css/master.css', 'r', encoding='utf-8') as f:
    text = f.read()

import re
matches = [m.start() for m in re.finditer(r'offcanvas__right[^{]*\{[^}]*\}', text)]
for idx in matches:
    print(text[idx:idx+250].replace('\n', ' '))

