with open('scratch_about_offcanvas.html', 'r', encoding='utf-8') as f:
    text = f.read()

# Let's inspect the active (non-commented) HTML inside offcanvas__body
import re
# remove HTML comments to see the clean markup
clean = re.sub(r'<!--.*?-->', '', text, flags=re.DOTALL)
print(clean)

