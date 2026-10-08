"""Run with Python + playwright and Chrome (or Playwright's installed Chromium).
Tests real locally bundled editor assets without using the application database.
"""
import os
import html
from pathlib import Path
import shutil
from http.server import SimpleHTTPRequestHandler, ThreadingHTTPServer
from threading import Thread

from playwright.sync_api import sync_playwright, expect

PUBLIC = Path(__file__).resolve().parents[2] / 'public'
FIXTURE = '''<!doctype html><html lang="en"><head><meta charset="utf-8">
<link rel="stylesheet" href="/vendor/quill/quill.snow.css">
<link rel="stylesheet" href="/css/admin.css">
<script src="/vendor/quill/quill.js" defer></script><script src="/js/admin.js" defer></script>
</head><body><main style="max-width:800px;padding:24px;margin:auto">
<form class="stack" id="test-form">
<label>Title<input name="title" value="Example"></label>
<label>Description<textarea data-wysiwyg name="description" required maxlength="5000"></textarea><small>Write your description.</small></label>
<label>Existing content<textarea data-wysiwyg name="existing" maxlength="5000">&lt;h3&gt;Welcome&lt;/h3&gt;&lt;p&gt;Care &lt;strong&gt;bold&lt;/strong&gt; and &lt;em&gt;gentle&lt;/em&gt;.&lt;/p&gt;&lt;ul&gt;&lt;li&gt;First&lt;/li&gt;&lt;li&gt;Second&lt;/li&gt;&lt;/ul&gt;</textarea></label>
<div data-team-members></div><p data-team-empty>No team members</p>
<button type="button" data-add-team-member>Add team member</button>
<template data-team-member-template><article data-team-member><h3 data-member-heading>New team member</h3><label>Name<input data-member-name name="team[__KEY__][name]"></label><label>Biography<textarea data-wysiwyg name="team[__KEY__][body]"></textarea><small>Biography help</small></label><button type="button" data-remove-team-member>Delete member</button></article></template>
<button type="submit">Save</button><button type="reset">Reset</button>
</form></main><script>document.querySelector('form').addEventListener('submit', event => { event.preventDefault(); window.saved = Object.fromEntries(new FormData(event.target)); });</script></body></html>'''


class Handler(SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=str(PUBLIC), **kwargs)

    def do_GET(self):
        if self.path == '/fixture':
            self.send_response(200)
            self.send_header('Content-Type', 'text/html; charset=utf-8')
            self.end_headers()
            self.wfile.write(FIXTURE.encode())
        else:
            super().do_GET()

    def log_message(self, *args):
        pass


def normalized(value):
    return html.unescape(value).replace('\u00a0', ' ')


def run():
    server = ThreadingHTTPServer(('127.0.0.1', 0), Handler)
    Thread(target=server.serve_forever, daemon=True).start()
    try:
        with sync_playwright() as playwright:
            executable = os.environ.get('BROWSER_PATH') or shutil.which('google-chrome') or shutil.which('chromium')
            browser = playwright.chromium.launch(executable_path=executable, headless=True)
            page = browser.new_page()
            errors = []
            page.on('pageerror', lambda error: errors.append(str(error)))
            url = f'http://127.0.0.1:{server.server_port}/fixture'
            page.goto(url)
            editor = page.get_by_role('textbox', name='Description', exact=True)
            expect(editor).to_be_visible()
            assert not editor.evaluate('element => Boolean(element.closest("label"))')

            # The original regression: a first click on blank space must keep focus.
            editor.click(position={'x': 100, 'y': 100})
            expect(editor).to_be_focused()
            page.keyboard.type('Typing works immediately.')
            expect(editor).to_contain_text('Typing works immediately.')
            assert 'Typing works immediately.' in normalized(page.locator('textarea[name=description]').input_value())
            page.locator('.rich-text-field > label').first.click()
            expect(editor).to_be_focused()

            # Formatting toolbar and history must preserve selection and saved HTML.
            page.keyboard.press('Control+a')
            toolbar = page.get_by_role('toolbar', name='Description formatting')
            toolbar.get_by_role('button', name='Bold', exact=True).click()
            assert '<strong>Typing works immediately.</strong>' in normalized(page.locator('textarea[name=description]').input_value())
            toolbar.get_by_role('button', name='Undo', exact=True).click()
            assert '<strong>' not in normalized(page.locator('textarea[name=description]').input_value())
            toolbar.get_by_role('button', name='Redo', exact=True).click()
            assert '<strong>' in normalized(page.locator('textarea[name=description]').input_value())
            toolbar.get_by_role('button', name='Bulleted list', exact=True).click()
            assert '<ul>' in normalized(page.locator('textarea[name=description]').input_value())
            page.get_by_role('button', name='Save', exact=True).click()
            assert '<ul>' in page.evaluate('window.saved.description')

            # Existing CMS markup survives initialization and form submission.
            existing = page.locator('textarea[name=existing]').input_value()
            for tag in ['<h3>Welcome</h3>', '<strong>bold</strong>', '<em>gentle</em>', '<ul>', '<li>First</li>']:
                assert tag in existing, (tag, existing)

            # Rich paste keeps supported formatting and strips executable content.
            editor.click()
            page.keyboard.press('Control+a')
            editor.evaluate("""element => {
                const clipboard = new DataTransfer();
                clipboard.setData('text/html', '<p onclick="alert(1)">Pasted <strong>formatting</strong></p><script>window.pasteExecuted=true</script>');
                clipboard.setData('text/plain', 'Pasted formatting');
                element.dispatchEvent(new ClipboardEvent('paste', {clipboardData: clipboard, bubbles: true, cancelable: true}));
            }""")
            expect(editor).to_contain_text('Pasted formatting')
            pasted = normalized(page.locator('textarea[name=description]').input_value())
            assert '<strong>formatting</strong>' in pasted
            assert 'onclick' not in pasted and '<script' not in pasted
            assert page.evaluate('window.pasteExecuted') is None

            # Required and length validation point to the editor, never a hidden field.
            editor.fill('')
            page.evaluate('window.saved = null')
            page.get_by_role('button', name='Save', exact=True).click()
            assert page.evaluate('window.saved') is None
            expect(editor).to_be_focused()
            expect(page.get_by_role('alert').first).to_contain_text('Please enter description.')
            editor.fill('Valid again')
            expect(page.get_by_role('alert').first).to_be_hidden()
            page.locator('textarea[name=description]').evaluate('element => element.maxLength = 20')
            editor.fill('A description that exceeds the limit')
            page.get_by_role('button', name='Save', exact=True).click()
            expect(page.get_by_role('alert').first).to_contain_text('too long')
            assert page.evaluate('window.saved') is None
            page.locator('textarea[name=description]').evaluate('element => element.maxLength = 5000')
            editor.fill('Valid description')

            # Newly inserted team biographies get independent editable instances.
            page.get_by_role('button', name='Add team member', exact=True).click()
            biography = page.get_by_role('textbox', name='Biography', exact=True)
            biography.click(position={'x': 60, 'y': 80})
            expect(biography).to_be_focused()
            page.keyboard.type('New practitioner biography')
            page.get_by_role('button', name='Save', exact=True).click()
            saved = page.evaluate('window.saved')
            assert any('New practitioner biography' in normalized(value) for key, value in saved.items() if key.endswith('[body]'))

            # Removing an invalid dynamic field must not block the remaining form.
            page.locator('[data-team-member] textarea').evaluate('element => element.maxLength = 10')
            biography.fill('This biography exceeds its limit')
            page.once('dialog', lambda dialog: dialog.accept())
            page.get_by_role('button', name='Delete member', exact=True).click()
            page.evaluate('window.saved = null')
            page.get_by_role('button', name='Save', exact=True).click()
            assert page.evaluate('window.saved') is not None

            # Reset reloads each source value, including its original formatting.
            page.get_by_role('button', name='Reset', exact=True).click()
            expect(editor).to_have_text('')
            expect(page.get_by_role('textbox', name='Existing content', exact=True)).to_contain_text('Welcome')
            page.set_viewport_size({'width': 390, 'height': 844})
            assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
            assert not errors, errors

            # Library failure leaves the ordinary textarea editable and submittable.
            fallback = browser.new_page()
            fallback.route('**/vendor/quill/quill.js', lambda route: route.abort())
            fallback.goto(url)
            textarea = fallback.locator('textarea[name=description]')
            expect(textarea).to_be_visible()
            textarea.fill('Fallback still works')
            fallback.get_by_role('button', name='Save', exact=True).click()
            assert fallback.evaluate('window.saved.description') == 'Fallback still works'
            browser.close()
            print('PASS: first-click typing, labels, formatting, undo/redo, HTML submission, existing content, validation, dynamic fields, reset, mobile layout, and library fallback.')
    finally:
        server.shutdown()


if __name__ == '__main__':
    run()
