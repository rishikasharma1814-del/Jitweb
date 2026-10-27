import re
import difflib

with open('c:/Users/nehas/Neha/Jitweb/business-coaching.html', 'r', encoding='utf-8') as f:
    bc = f.read()
with open('c:/Users/nehas/Neha/Jitweb/temp_v32.html', 'r', encoding='utf-8') as f:
    v32 = f.read()

bc_script = re.search(r'<script id="jit-website-v28-viewport-script">.*?</script>', bc, re.DOTALL)
v32_script = re.search(r'<script id="jit-website-v28-viewport-script">.*?</script>', v32, re.DOTALL)

print("BC script found:", bool(bc_script))
print("V32 script found:", bool(v32_script))

if bc_script and v32_script:
    diff = list(difflib.unified_diff(
        bc_script.group(0).splitlines(keepends=True),
        v32_script.group(0).splitlines(keepends=True),
        fromfile='business-coaching.html',
        tofile='temp_v32.html'
    ))
    print("SCRIPT DIFF:")
    print(''.join(diff))
