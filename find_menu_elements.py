with open('business-coaching.html', 'r', encoding='utf-8') as f:
    text = f.read()

import re
# find all buttons or links with menu in class, id, or data attribute
matches = re.findall(r'<[^>]*(?:menu|offcanvas)[^>]*>', text, re.IGNORECASE)
print(f"Total menu elements found: {len(matches)}")
for m in matches[:15]:
    print(m)

