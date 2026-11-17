<?php
/** @license GPL-2.0-only */
if (!defined('DOKU_INC')) die();

/** Inline render instruction emitted by section.php; no independent wiki syntax. */
class syntax_plugin_foldedsection_label extends DokuWiki_Syntax_Plugin
{
    public function getType() { return 'substition'; }
    public function getSort() { return 391; }
    public function connectTo($mode) {}
    public function handle($match, $state, $pos, Doku_Handler $handler) { return [$match]; }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            $renderer->cdata($data[0]);
            return true;
        }
        $helper = plugin_load('helper', 'foldedsection');
        $id = $helper->prepareId($renderer);
        // Start expanded; JS only collapses after attaching working event handlers.
        // Without JS this remains an ordinary fragment link to visible content.
        $renderer->doc .= '<a class="foldedsection-toggle" href="#' . $id . '">'
            . $helper->labelHtml($data[0]) . '<span class="foldedsection-indicator" aria-hidden="true"></span></a>';
        return true;
    }
}
