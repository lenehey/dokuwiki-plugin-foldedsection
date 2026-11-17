<?php
/** @license GPL-2.0-only */
if (!defined('DOKU_INC')) die();

class syntax_plugin_foldedsection_section extends DokuWiki_Syntax_Plugin
{
    public function getType() { return 'container'; }
    public function getPType() { return 'stack'; }
    public function getSort() { return 390; }
    public function getAllowedTypes()
    {
        return ['container', 'substition', 'protected', 'disabled', 'paragraphs', 'formatting'];
    }

    public function connectTo($mode)
    {
        // Also connect inside paragraph-bearing plugin containers (e.g. Wrap's
        // <div indent> and <WRAP>). Inline/protected modes cannot contain our div.
        if ($mode !== 'base') {
            if (strpos($mode, 'plugin_') !== 0 || $mode === 'plugin_foldedsection_section') return;
            $parent = plugin_load('syntax', substr($mode, 7));
            if (!$parent || $parent->getPType() !== 'stack') return;
        }
        $this->Lexer->addEntryPattern(
            '\+\+==[^\n|]+\|[ \t]*\n(?=[\s\S]*?\n[ \t]*\+\+==[ \t]*(?=\n|$))',
            $mode,
            'plugin_foldedsection_section'
        );
    }

    public function postConnect()
    {
        // Some block modes consume their trailing newline before returning here.
        $this->Lexer->addExitPattern('(?:\n|(?<=\n)|^)[ \t]*\+\+==[ \t]*(?=\n|$)', 'plugin_foldedsection_section');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        if ($state === DOKU_LEXER_ENTER) {
            $label = trim(substr($match, 4, strpos($match, '|') - 4));
            if ($label === '') $label = 'More details';
            // The inline label precedes the stack instruction. DokuWiki then closes
            // the introductory paragraph before opening the block, keeping valid HTML.
            $handler->addCall('plugin', ['foldedsection_label', [$label], DOKU_LEXER_SPECIAL, $match], $pos);
        } elseif ($state === DOKU_LEXER_UNMATCHED) {
            $handler->addCall('cdata', [$match], $pos);
            return false;
        }
        return [$state];
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') return false;
        if ($data[0] === DOKU_LEXER_ENTER) {
            $helper = plugin_load('helper', 'foldedsection');
            $renderer->doc .= '<div class="foldedsection-body" id="' . $helper->takeId($renderer) . '">';
        } elseif ($data[0] === DOKU_LEXER_EXIT) {
            $renderer->doc .= '</div>';
        }
        return true;
    }
}
