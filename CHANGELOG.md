# Changelog

## 0.1.1 — 2026-11-17

- Recognize `++==` sections inside paragraph-bearing plugin containers, including Wrap's `<div indent>` and `<WRAP>` blocks.
- Prevent the original folded plugin from consuming the new delimiters in those containers.
- Add the reported indented comparison as a regression fixture and test with both folded and Wrap enabled.

## 0.1.0 — 2026-11-17

- Introduce `++== Label |` / `++==` delimiters.
- Place labels inline and expandable block content beneath their paragraph.
- Support bold and italic labels, including nested combinations.
- Add folded-style dotted borders and disclosure triangles.
- Add keyboard controls, expanded-state announcements, printing, and a no-JavaScript fallback.
