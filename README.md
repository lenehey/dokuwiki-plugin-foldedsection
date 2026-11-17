# Folded Section for DokuWiki

Place a disclosure label beside ordinary paragraph text. Expand a separate, dotted-border block directly below that paragraph. Labels support **bold** and *italics*, and the body supports normal DokuWiki block content.

```text
**This is a heading** ++== //More details// |
Expanded text expanded text expanded text.

Another paragraph:
  * First item
  * Second item
++==
```

The introductory bold text and italic disclosure label appear together on one line (subject to normal wrapping). Clicking the label or its triangle opens or closes the bordered section beneath it. The introductory text remains ordinary paragraph content, not a heading.

## Syntax

- Open with `++== Label |` at the end of an ordinary paragraph. The body starts on the next line.
- Close with `++==` on a line by itself.
- Use `**bold**`, `//italic//`, or `**//both//**` inside the label.
- Links and other wiki syntax in labels are displayed literally. A `|` cannot occur inside a label.
- Body content supports paragraphs, formatting, links, lists, tables, code blocks, and compatible syntax plugins.
- Sections are initially collapsed when JavaScript is available.

An opening label can also occupy its own paragraph:

```text
++== **Additional information** |
The expanded content.
++==
```

For version 0.1, use sections in ordinary top-level paragraphs. Nested folded sections, labels inside headings/lists/tables, and text after the opening `|` on the same line are unsupported. A missing closing delimiter leaves the opening syntax as ordinary text. Keep bold/italic markup in the introduction balanced before the opening delimiter.

## Install

Copy this repository into `lib/plugins/foldedsection/` in your DokuWiki installation. The folder name must be `foldedsection`, without a hyphen. Enable it in Extension Manager if necessary.

The original **folded** plugin is not required; both plugins can be installed together. This plugin's parser takes priority for the `++==` opener.

## Appearance and accessibility

The expanded area uses the original folded plugin's visual conventions: `0.5em` padding and a one-pixel dotted border in the template's border color. A small triangle beside the label indicates the section state. CSS classes are separate so the two plugins can be styled independently:

- `.foldedsection-toggle` — clickable label
- `.foldedsection-indicator` — disclosure triangle
- `.foldedsection-body` — expanded block

The label uses DokuWiki's normal link appearance. JavaScript adds button semantics, `aria-controls`, and `aria-expanded`. Enter and Space operate the control. Without JavaScript, all section content stays visible. Printing includes collapsed content. Fragment links into a section open that section.

Labels interpret only paired bold/italic markers. HTML is escaped, and no arbitrary plugins or links are executed within labels. Body content is handled by DokuWiki's normal parser and renderers; this also preserves body links in metadata and search indexing. Non-HTML renderers receive the label text and the body without disclosure controls.

## Development

This is an initial implementation. Test on a staging wiki before production use.

Run the integration checks against a DokuWiki installation containing the plugin:

```sh
php tests/integration.php /path/to/dokuwiki
node --check script.js
```

The integration checks exercise DokuWiki's actual parser/renderers: paragraph placement, body blocks, label formatting and escaping, multiple sections, unique IDs, protected code, malformed input, and coexistence with the original folded plugin when installed.

## License

GPL-2.0-only. See [LICENSE](LICENSE).
