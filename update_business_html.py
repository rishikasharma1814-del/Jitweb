with open('business-coaching.html', 'r', encoding='utf-8') as f:
    text = f.read()

# Fix desktop demo-nav double indent
old_nav_indent = """      body.j26-site #j27-demo-nav {
        margin-left: var(--j26-rail) !important;
        width: calc(100% - var(--j26-rail)) !important;
      }"""

new_nav_indent = """      body.j26-site #j27-demo-nav {
        margin-left: 0 !important;
        width: 100% !important;
      }"""

text = text.replace(old_nav_indent, new_nav_indent)

with open('business-coaching.html', 'w', encoding='utf-8') as f:
    f.write(text)

print("SUCCESS: Updated business-coaching.html")

