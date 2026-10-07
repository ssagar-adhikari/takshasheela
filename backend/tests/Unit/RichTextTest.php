<?php

namespace Tests\Unit;

use App\Support\RichText;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function test_allowed_formatting_is_preserved_and_unsafe_markup_is_removed(): void
    {
        $html = RichText::sanitize('<p onclick="alert(1)">Welcome <strong style="color:red">home</strong>.</p><script>alert(2)</script><iframe src="https://example.com"></iframe>');

        $this->assertSame('<p>Welcome <strong>home</strong>.</p>', $html);
        $this->assertStringNotContainsString('onclick', $html);
        $this->assertStringNotContainsString('style=', $html);
        $this->assertStringNotContainsString('script', $html);
        $this->assertStringNotContainsString('iframe', $html);
    }

    public function test_plain_text_is_rendered_as_safe_paragraphs(): void
    {
        $html = RichText::render("First & safe paragraph.\n\nSecond paragraph.");

        $this->assertSame('<p>First &amp; safe paragraph.</p><p>Second paragraph.</p>', $html);
    }
}
