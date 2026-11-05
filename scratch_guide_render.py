with open('business-coaching.html', 'r', encoding='utf-8') as f:
    text = f.read()

import re
m = re.search(r'function\s+renderGuide\b|tour\.visible|j27-guide-copy', text)
if m:
    idx = m.start()
    print("Found snippet:")
    print(text[idx-50:idx+600].encode('ascii', 'replace').decode('ascii'))

