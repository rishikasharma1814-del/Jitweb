with open('business-coaching.html', 'r', encoding='utf-8') as f:
    text = f.read()

import re
matches = re.findall(r'<[^>]*(?:menu|offcanvas)[^>]*>', text, re.IGNORECASE)
for m in matches[15:]:
    print(m)

