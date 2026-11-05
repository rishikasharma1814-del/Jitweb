with open('scratch_index_offcanvas.html', 'r', encoding='utf-8') as f:
    text = f.read()

import re
clean = re.sub(r'<!--.*?-->', '', text, flags=re.DOTALL)
print(clean)

