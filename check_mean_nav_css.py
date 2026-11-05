with open('assets/css/jit-offcanvas.css', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('.offcanvas__menu-wrapper.mean-container .mean-nav ul li a')
print("Snippet from jit-offcanvas.css:")
print(text[idx:idx+800])

