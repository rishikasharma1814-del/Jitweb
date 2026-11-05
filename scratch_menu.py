with open('business-coaching.html', 'r', encoding='utf-8') as f:
    text = f.read()

import re
matches = [m.start() for m in re.finditer(r'j26-menu', text)]
print("Total occurrences of j26-menu:", len(matches))
for idx in matches[:10]:
    print("---", idx, "---")
    print(text[max(0, idx-50):min(len(text), idx+100)].replace('\n', ' '))

