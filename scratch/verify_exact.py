for fpath in ['helpdesk.html', 'product/helpdesk.html']:
    with open(fpath, 'r', encoding='utf-8') as f:
        text = f.read()
    print(f"=== Checking {fpath} ===")
    assert 'width: 62px !important;' in text, "62px width missing!"
    assert 'mix-blend-mode: exclusion !important;' in text, "exclusion mode missing!"
    assert 'background: transparent !important;' in text, "transparent bg missing!"
    assert 'margin-left: 62px !important;' in text, "#app margin-left missing!"
    assert '(function(){\n\'use strict\';' in text, "IIFE start missing!"
    assert 'render();\n})();</script>' in text, "IIFE end missing!"
    assert 'initOffcanvasDirect' in text, "Offcanvas direct listener missing!"
    print("All assertions passed!")

