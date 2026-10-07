<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class RichText
{
    private const ALLOWED_TAGS = ['p', 'br', 'strong', 'b', 'em', 'i', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'blockquote'];

    private const REMOVE_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'form', 'input', 'button'];

    public static function sanitize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '' || ! preg_match('/<[a-z][^>]*>/i', $value)) {
            return $value;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><div id="rich-text-root">'.$value.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('rich-text-root');
        if (! $root) {
            return '';
        }

        self::cleanChildren($root);

        $html = '';
        foreach ($root->childNodes as $child) {
            $html .= $document->saveHTML($child);
        }

        return trim($html);
    }

    public static function render(?string $value): string
    {
        $value = self::sanitize($value) ?? '';
        if ($value === '') {
            return '';
        }

        if (preg_match('/<[a-z][^>]*>/i', $value)) {
            return $value;
        }

        $paragraphs = preg_split('/\R\s*\R/u', trim($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return implode('', array_map(
            fn (string $paragraph) => '<p>'.nl2br(e(trim($paragraph))).'</p>',
            $paragraphs,
        ));
    }

    private static function cleanChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node instanceof DOMElement) {
                $tag = strtolower($node->tagName);
                if (in_array($tag, self::REMOVE_WITH_CONTENT, true)) {
                    $parent->removeChild($node);

                    continue;
                }

                self::cleanChildren($node);
                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    while ($node->firstChild) {
                        $parent->insertBefore($node->firstChild, $node);
                    }
                    $parent->removeChild($node);

                    continue;
                }

                while ($node->attributes->length > 0) {
                    $node->removeAttributeNode($node->attributes->item(0));
                }
            } elseif ($node->nodeType === XML_COMMENT_NODE) {
                $parent->removeChild($node);
            }
        }
    }
}
