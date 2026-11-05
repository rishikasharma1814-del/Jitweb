with open('assets/css/master.css', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('.offcanvas__area')
print("Found offcanvas in master.css at:", idx)
print(text[idx:idx+2500])

