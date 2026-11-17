<?php
/** @license GPL-2.0-only */
if (!defined('DOKU_INC')) die();

class helper_plugin_foldedsection extends DokuWiki_Plugin
{
    private $pending;
    private static $sequence = 0;

    public function prepareId(Doku_Renderer $renderer)
    {
        if ($this->pending === null) $this->pending = new SplObjectStorage();
        $id = 'foldedsection-' . (++self::$sequence);
        $this->pending[$renderer] = $id;
        return $id;
    }

    public function takeId(Doku_Renderer $renderer)
    {
        $id = $this->pending[$renderer];
        $this->pending->detach($renderer);
        return $id;
    }

    /** Only paired **bold** and //italic// markup is interpreted. */
    public function labelHtml($label)
    {
        $parts = preg_split('~(\*\*|//)~', $label, -1, PREG_SPLIT_DELIM_CAPTURE);
        $stack = [['marker' => '', 'html' => '']];
        foreach ($parts as $part) {
            $top = count($stack) - 1;
            if ($part !== '**' && $part !== '//') {
                $stack[$top]['html'] .= htmlspecialchars($part, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            } elseif ($stack[$top]['marker'] === $part) {
                $frame = array_pop($stack);
                $tag = $part === '**' ? 'strong' : 'em';
                $stack[count($stack) - 1]['html'] .= '<' . $tag . '>' . $frame['html'] . '</' . $tag . '>';
            } else {
                $stack[] = ['marker' => $part, 'html' => ''];
            }
        }
        // Unpaired delimiters remain visible, without generating unclosed HTML.
        while (count($stack) > 1) {
            $frame = array_pop($stack);
            $stack[count($stack) - 1]['html'] .= $frame['marker'] . $frame['html'];
        }
        return $stack[0]['html'];
    }
}
