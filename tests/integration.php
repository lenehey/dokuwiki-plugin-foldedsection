<?php
/** Run: php tests/integration.php /path/to/dokuwiki */
$root = realpath($argv[1] ?? '');
if (!$root || !is_file($root . '/inc/init.php')) {
    fwrite(STDERR, "Provide the path to a DokuWiki installation with this plugin installed.\n");
    exit(2);
}
define('DOKU_INC', str_replace('\\', '/', $root) . '/');
define('NOSESSION', 1);
require DOKU_INC . 'inc/init.php';
set_exception_handler(function ($error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(1);
});
$ID = 'foldedsection:test';
$ACT = 'show';
$checks = 0;
function fsCheck($condition, $message)
{
    global $checks;
    $checks++;
    if (!$condition) throw new RuntimeException($message);
}
function renderWiki($source)
{
    $info = [];
    return p_render('xhtml', p_get_instructions($source), $info);
}
function doc($html)
{
    $dom = new DOMDocument();
    @$dom->loadHTML('<!doctype html><html><body><div class="dokuwiki">' . $html . '</div></body></html>');
    return new DOMXPath($dom);
}

$source = "**This is a heading** ++== //More details// |\nExpanded **bold** text.\n\nSecond paragraph.\n\n  * First item\n  * Second item\n\n^ Name ^ Value ^\n| One | Two |\n++==\n\nFollowing paragraph.";
$html = renderWiki($source);
$xp = doc($html);
fsCheck($xp->query('//p[strong="This is a heading"]/a[@class="foldedsection-toggle"]/em')->length === 1, 'Label must share the introductory paragraph and preserve italics');
fsCheck($xp->query('//a[@class="foldedsection-toggle"]/ancestor::p/following-sibling::*[1][self::div[@class="foldedsection-body"]]')->length === 1, 'Body must immediately follow the introductory paragraph');
fsCheck(!preg_match('~<p>\s*<div~', $html), 'No block may open inside a paragraph');
fsCheck($xp->query('//div[@class="foldedsection-body"]/p')->length === 2, 'Body must preserve multiple paragraphs');
fsCheck($xp->query('//div[@class="foldedsection-body"]//strong')->length === 1, 'Body must parse formatting');
fsCheck($xp->query('//div[@class="foldedsection-body"]//li')->length === 2, 'Body must parse lists');
fsCheck($xp->query('//div[@class="foldedsection-body"]//table')->length === 1, 'Body must parse tables');
fsCheck($xp->query('//div[@class="foldedsection-body"]//p[contains(., "Following")]')->length === 0, 'Following text must be outside the body');
fsCheck(!str_contains($html, ' hidden'), 'Content must be visible without JavaScript');

$source = "Intro ++== **//Both//** |\nOne\n++==\n\nIntro ++== Two |\nTwo\n++==";
$html = renderWiki($source);
$xp = doc($html);
fsCheck($xp->query('//a[@class="foldedsection-toggle"]/strong/em')->length === 1, 'Nested bold and italic must work');
$ids = [];
foreach ($xp->query('//a[@class="foldedsection-toggle"]') as $label) {
    $id = substr($label->getAttribute('href'), 1);
    fsCheck($xp->query('//div[@id="' . $id . '"]')->length === 1, 'Every label must target exactly one body');
    $ids[] = $id;
}
fsCheck(count(array_unique($ids)) === 2, 'Sections must have unique IDs');
$htmlAgain = renderWiki($source);
fsCheck(!str_contains($htmlAgain, 'id="' . $ids[0] . '"'), 'Repeated rendering/includes must not reuse IDs');

$helper = plugin_load('helper', 'foldedsection');
fsCheck($helper->labelHtml('<img src=x onerror=alert(1)>') === '&lt;img src=x onerror=alert(1)&gt;', 'HTML in labels must be escaped');
fsCheck($helper->labelHtml('**unfinished') === '**unfinished', 'Unpaired formatting must remain literal');
fsCheck($helper->labelHtml('[[page]]') === '[[page]]', 'Labels must not create embedded links');
fsCheck($helper->labelHtml('**Bold** and //italic//') === '<strong>Bold</strong> and <em>italic</em>', 'Independent formatting must work');

$html = renderWiki("++== Label |\n<code>\n++==\n</code>\nAfter code\n++==");
$xp = doc($html);
fsCheck($xp->query('//div[@class="foldedsection-body"]//pre[contains(., "++==")]')->length === 1, 'Delimiter inside code must not close the section');
fsCheck($xp->query('//div[@class="foldedsection-body"]//p[contains(., "After code")]')->length === 1, 'Section must remain open after code');

$html = renderWiki("Intro ++== Missing close |\nPlain text.");
fsCheck(!str_contains($html, 'foldedsection-toggle'), 'Incomplete sections must remain ordinary text');
$html = renderWiki("<code>\nIntro ++== Not a section |\nExample\n++==\n</code>");
fsCheck(!str_contains($html, 'foldedsection-toggle'), 'Code examples must remain untouched');
$html = renderWiki("Intro ++== Label |\n[[start|Working body link]]\n++==");
fsCheck(doc($html)->query('//div[@class="foldedsection-body"]//a')->length === 1, 'Body links must remain functional');

// Coexistence check when the original folded plugin is installed in this test wiki.
if (is_dir(DOKU_PLUGIN . 'folded')) {
    $html = renderWiki("Old ++ old label | old content ++\n\nNew ++== New label |\nNew content\n++==");
    $xp = doc($html);
    fsCheck($xp->query('//a[contains(@class,"folder")]')->length === 1, 'Original folded inline syntax must still work');
    fsCheck($xp->query('//a[@class="foldedsection-toggle"]')->length === 1, 'New delimiter must take priority over original folded syntax');
}

echo "Passed $checks integration checks.\n";

