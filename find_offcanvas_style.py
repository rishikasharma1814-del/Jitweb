with open('style.css', 'r', encoding='utf-8') as f:
    text = f.read()

import re
matches = [m.start() for m in re.finditer(r'offcanvas', text, re.IGNORECASE)]
print("Total occurrences of offcanvas in style.css:", len(matches))
for idx in matches[:10]:
    print(text[max(0, idx-30):min(len(text), idx+120)].replace('\n', ' '))

