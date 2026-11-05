with open('assets/css/master.css', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('.offcanvas__right .shape-1')
print(text[idx:idx+2500])

