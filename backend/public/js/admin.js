document.querySelector('[data-menu]')?.addEventListener('click', (event) => {
    const button = event.currentTarget;
    const expanded = button.getAttribute('aria-expanded') !== 'true';
    button.setAttribute('aria-expanded', String(expanded));
    document.querySelector('.sidebar').classList.toggle('is-open', expanded);
});

const sidebarCollapse = document.querySelector('[data-sidebar-collapse]');

function updateSidebarCollapseButton() {
    if (!sidebarCollapse) return;
    const collapsed = document.documentElement.classList.contains('sidebar-is-collapsed');
    sidebarCollapse.setAttribute('aria-expanded', String(!collapsed));
    sidebarCollapse.title = collapsed ? 'Expand menu' : 'Collapse menu';
    sidebarCollapse.querySelector('.sr-only').textContent = collapsed ? 'Expand navigation' : 'Collapse navigation';
}

sidebarCollapse?.addEventListener('click', () => {
    const collapsed = document.documentElement.classList.toggle('sidebar-is-collapsed');
    try {
        localStorage.setItem('takshasheela-sidebar-collapsed', String(collapsed));
    } catch (error) {
        // The menu still works when browser storage is unavailable.
    }
    updateSidebarCollapseButton();
});

updateSidebarCollapseButton();

document.querySelectorAll('[data-nav-group-toggle]').forEach((button) => {
    const items = document.getElementById(button.getAttribute('aria-controls'));
    if (!items) return;

    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') !== 'true';
        button.setAttribute('aria-expanded', String(expanded));
        items.hidden = !expanded;
    });
});

function initializeImagePicker(picker) {
    const input = picker.querySelector('[data-image-input]');
    const preview = picker.querySelector('[data-image-preview]');
    const feedback = picker.querySelector('[data-image-feedback]');
    const remove = picker.querySelector('[data-image-remove]');
    const initialSource = preview.getAttribute('src');
    let objectUrl;
    input.addEventListener('change', () => {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        const file = input.files[0];
        input.setCustomValidity('');
        feedback.textContent = '';
        if (!file) {
            if (initialSource) preview.src = initialSource;
            preview.hidden = !initialSource;
            return;
        }
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
            input.setCustomValidity('Choose a JPG, PNG, or WebP image of 2 MB or less.');
            feedback.textContent = input.validationMessage;
            input.reportValidity();
            return;
        }
        objectUrl = URL.createObjectURL(file);
        preview.src = objectUrl;
        preview.hidden = false;
        feedback.textContent = file.name + ' — ready to upload when you save.';
        if (remove) remove.checked = false;
    });
}

document.querySelectorAll('[data-image-picker]').forEach(initializeImagePicker);

const teamMembers = document.querySelector('[data-team-members]');
const teamTemplate = document.querySelector('[data-team-member-template]');
const addTeamMember = document.querySelector('[data-add-team-member]');
const emptyTeam = document.querySelector('[data-team-empty]');

function refreshTeamState() {
    if (!teamMembers) return;
    const count = teamMembers.querySelectorAll('[data-team-member]').length;
    if (emptyTeam) emptyTeam.hidden = count !== 0;
    if (addTeamMember) {
        addTeamMember.disabled = count >= 50;
        addTeamMember.title = count >= 50 ? 'The maximum is 50 team members.' : '';
    }
}

addTeamMember?.addEventListener('click', () => {
    if (teamMembers.querySelectorAll('[data-team-member]').length >= 50) return;
    const randomPart = globalThis.crypto?.randomUUID?.().toLowerCase() ?? Math.random().toString(36).slice(2);
    const key = 'member-' + Date.now().toString(36) + '-' + randomPart;
    const wrapper = document.createElement('div');
    wrapper.append(teamTemplate.content.cloneNode(true));
    const member = wrapper.firstElementChild;
    member.innerHTML = member.innerHTML.replaceAll('__KEY__', key);
    teamMembers.append(member);
    member.querySelectorAll('[data-image-picker]').forEach(initializeImagePicker);
    member.querySelectorAll('textarea[data-wysiwyg]').forEach(initializeRichTextEditor);
    member.querySelector('[data-member-name]').focus();
    refreshTeamState();
});

teamMembers?.addEventListener('input', (event) => {
    if (!event.target.matches('[data-member-name]')) return;
    const member = event.target.closest('[data-team-member]');
    member.querySelector('[data-member-heading]').textContent = event.target.value.trim() || 'New team member';
});

teamMembers?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-team-member]');
    if (!button) return;
    const member = button.closest('[data-team-member]');
    const name = member.querySelector('[data-member-name]').value.trim() || 'this team member';
    if (!window.confirm('Delete ' + name + '? The deletion takes effect when you save.')) return;
    member.remove();
    refreshTeamState();
});

refreshTeamState();

const richTextAllowedTags = new Set(['P', 'BR', 'STRONG', 'B', 'EM', 'I', 'UL', 'OL', 'LI', 'H2', 'H3', 'H4', 'BLOCKQUOTE']);
const richTextRemovedTags = new Set(['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT', 'EMBED', 'SVG', 'MATH', 'FORM', 'INPUT', 'BUTTON']);

function sanitizeRichTextFragment(html) {
    const template = document.createElement('template');
    template.innerHTML = html;
    const clean = (parent) => {
        Array.from(parent.childNodes).forEach((node) => {
            if (node.nodeType === Node.COMMENT_NODE) {
                node.remove();
                return;
            }
            if (node.nodeType !== Node.ELEMENT_NODE) return;
            if (richTextRemovedTags.has(node.tagName)) {
                node.remove();
                return;
            }
            clean(node);
            if (!richTextAllowedTags.has(node.tagName)) {
                node.replaceWith(...node.childNodes);
                return;
            }
            Array.from(node.attributes).forEach((attribute) => node.removeAttribute(attribute.name));
        });
    };
    clean(template.content);
    return template.innerHTML;
}

function plainTextToRichHtml(value) {
    const paragraphs = value.trim().split(/\n\s*\n/).filter(Boolean);
    return paragraphs.map((paragraph) => {
        const element = document.createElement('p');
        paragraph.split('\n').forEach((line, index) => {
            if (index) element.append(document.createElement('br'));
            element.append(document.createTextNode(line));
        });
        return element.outerHTML;
    }).join('');
}

let richTextEditorId = 0;

function initializeRichTextEditor(source) {
    // Keep a usable textarea if the editor library could not be loaded.
    if (source.dataset.wysiwygReady === 'true' || !window.Quill || source.disabled || source.readOnly) return;

    const label = source.closest('label');
    const name = label?.firstChild?.textContent?.trim() || 'Description';
    const id = 'rich-text-' + (++richTextEditorId);
    const wrapper = document.createElement('div');
    wrapper.className = 'wysiwyg';
    const toolbar = document.createElement('div');
    toolbar.className = 'wysiwyg-toolbar';
    toolbar.setAttribute('role', 'toolbar');
    toolbar.setAttribute('aria-label', name + ' formatting');
    toolbar.innerHTML = `
        <span class="ql-formats"><select class="ql-header" aria-label="Text style">
            <option selected value="">Paragraph</option><option value="2">Heading 2</option>
            <option value="3">Heading 3</option><option value="4">Heading 4</option>
        </select></span>
        <span class="ql-formats">
            <button type="button" class="ql-bold" aria-label="Bold" title="Bold (Ctrl+B)"></button>
            <button type="button" class="ql-italic" aria-label="Italic" title="Italic (Ctrl+I)"></button>
        </span>
        <span class="ql-formats">
            <button type="button" class="ql-list" value="bullet" aria-label="Bulleted list" title="Bulleted list"></button>
            <button type="button" class="ql-list" value="ordered" aria-label="Numbered list" title="Numbered list"></button>
            <button type="button" class="ql-blockquote" aria-label="Quote" title="Quote"></button>
        </span>
        <span class="ql-formats">
            <button type="button" class="ql-clean" aria-label="Clear formatting" title="Clear formatting"></button>
            <button type="button" class="ql-undo" aria-label="Undo" title="Undo (Ctrl+Z)">↶</button>
            <button type="button" class="ql-redo" aria-label="Redo" title="Redo (Ctrl+Shift+Z)">↷</button>
        </span>`;
    const container = document.createElement('div');
    const feedback = document.createElement('p');
    feedback.className = 'wysiwyg-feedback';
    feedback.id = id + '-feedback';
    feedback.setAttribute('role', 'alert');
    feedback.hidden = true;
    wrapper.append(toolbar, container, feedback);

    // An editor inside a <label> loses focus when the label activates its textarea.
    // Keep the interactive editor outside the label and forward label clicks explicitly.
    if (label) {
        const field = document.createElement('div');
        field.className = 'rich-text-field';
        label.before(field);
        field.append(label, wrapper);
        label.querySelectorAll('small').forEach((hint) => field.append(hint));
    } else {
        source.after(wrapper);
    }

    let editor;
    try {
        editor = new Quill(container, {
            theme: 'snow',
            placeholder: source.placeholder || 'Start typing here…',
            formats: ['header', 'bold', 'italic', 'list', 'blockquote'],
            modules: {
                toolbar: {
                    container: toolbar,
                    handlers: {
                        undo() { this.quill.history.undo(); },
                        redo() { this.quill.history.redo(); },
                    },
                },
                history: { delay: 500, maxStack: 100, userOnly: true },
            },
        });
        const html = /<[a-z][\s\S]*>/i.test(source.value)
            ? sanitizeRichTextFragment(source.value)
            : plainTextToRichHtml(source.value);
        editor.setContents(editor.clipboard.convert({ html }), 'silent');
        editor.history.clear();
    } catch (error) {
        wrapper.remove();
        return;
    }

    source.dataset.wysiwygReady = 'true';
    source.hidden = true;
    source.classList.add('wysiwyg-source');
    source.tabIndex = -1;
    editor.root.id = id;
    editor.root.setAttribute('role', 'textbox');
    editor.root.setAttribute('aria-label', name);
    editor.root.setAttribute('aria-multiline', 'true');
    editor.root.setAttribute('aria-required', String(source.required));
    editor.root.setAttribute('aria-describedby', feedback.id);
    editor.root.setAttribute('spellcheck', 'true');
    // Quill's generated picker replaces the native select; name its focus target too.
    toolbar.querySelector('.ql-picker-label')?.setAttribute('aria-label', 'Text style');

    const validate = (showError = false) => {
        let message = '';
        if (source.required && !editor.getText().trim()) message = 'Please enter ' + name.toLowerCase() + '.';
        if (source.maxLength > 0 && Array.from(source.value).length > source.maxLength) {
            message = 'This content is too long. Shorten it to fit the ' + source.maxLength.toLocaleString() + '-character limit (including formatting).';
        }
        source.setCustomValidity(message);
        editor.root.setAttribute('aria-invalid', String(Boolean(message)));
        if (showError || !feedback.hidden) {
            feedback.textContent = message;
            feedback.hidden = !message;
        }
        return !message;
    };
    const sync = () => {
        source.value = editor.getText().trim() === '' ? '' : sanitizeRichTextFragment(editor.getSemanticHTML()).trim();
        validate();
    };
    editor.on('text-change', () => {
        sync();
        source.dispatchEvent(new Event('input', { bubbles: true }));
    });
    editor.root.addEventListener('blur', sync);
    label?.addEventListener('click', (event) => {
        event.preventDefault();
        editor.focus();
    });
    source.addEventListener('invalid', (event) => {
        event.preventDefault();
        validate(true);
        if (!source.form || source.form.querySelector(':invalid') === source) editor.focus();
    });
    source.form?.addEventListener('submit', (event) => {
        if (!source.isConnected) return;
        sync();
        if (!validate(true)) {
            event.preventDefault();
            if (source.form.querySelector(':invalid') === source) editor.focus();
        }
    });
    source.form?.addEventListener('reset', () => {
        if (!source.isConnected) return;
        // The browser restores textarea defaults after dispatching the reset event.
        setTimeout(() => {
            const html = /<[a-z][\s\S]*>/i.test(source.value)
                ? sanitizeRichTextFragment(source.value) : plainTextToRichHtml(source.value);
            editor.setContents(editor.clipboard.convert({ html }), 'silent');
            editor.history.clear();
            feedback.hidden = true;
            sync();
        }, 0);
    });
    sync();
}

document.querySelectorAll('textarea[data-wysiwyg]').forEach(initializeRichTextEditor);
