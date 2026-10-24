with open('c:/Users/nehas/Neha/Jitweb/business-coaching.html', 'r', encoding='utf-8', errors='ignore') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'JITWebsiteInitialHash' in line:
        print(f'Line {i+1}: {line.strip()[:100]}')
        for j in range(max(0, i-5), min(len(lines), i+25)):
            print(f'  {j+1}: {lines[j].strip()[:100]}')


