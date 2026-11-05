with open('index.html', 'r', encoding='utf-8') as f:
    text = f.read()

i1 = text.find('class="offcanvas__area"')
i2 = text.find('<!-- Offcanvas area end -->')
print("Index.html offcanvas length:", i2 - i1)
with open('scratch_index_offcanvas.html', 'w', encoding='utf-8') as out:
    out.write(text[i1-10:i2+27])

with open('about.html', 'r', encoding='utf-8') as f:
    about_text = f.read()

a1 = about_text.find('class="offcanvas__area"')
a2 = about_text.find('<!-- Offcanvas area end -->')
print("About.html offcanvas length:", a2 - a1)
with open('scratch_about_offcanvas.html', 'w', encoding='utf-8') as out:
    out.write(about_text[a1-10:a2+27])

