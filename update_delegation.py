with open('business-coaching.html', 'r', encoding='utf-8') as f:
    text = f.read()

target = """        $('#open_offcanvas').on('click', function () {
          $('.offcanvas__area').css({ 'opacity': '1', 'visibility': 'visible' });
        });
        $('#close_offcanvas').on('click', function () {
          $('.offcanvas__area').css({ 'opacity': '0', 'visibility': 'hidden' });
        });"""

replacement = """        $(document).on('click', '#open_offcanvas, .header__nav-icon button, [data-j26-menu]', function (e) {
          e.preventDefault();
          $('.offcanvas__area').css({ 'opacity': '1', 'visibility': 'visible' });
        });
        $(document).on('click', '#close_offcanvas, .offcanvas__close button', function (e) {
          e.preventDefault();
          $('.offcanvas__area').css({ 'opacity': '0', 'visibility': 'hidden' });
        });
        $(document).on('keydown', function (e) {
          if (e.key === 'Escape') {
            $('.offcanvas__area').css({ 'opacity': '0', 'visibility': 'hidden' });
          }
        });"""

if target in text:
    text = text.replace(target, replacement)
    with open('business-coaching.html', 'w', encoding='utf-8') as f:
        f.write(text)
    print("SUCCESS: Updated menu click delegation in business-coaching.html")
else:
    print("WARNING: Exact target not found in business-coaching.html")

