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

function initializeRichTextEditor(source) {
    if (source.dataset.wysiwygReady === 'true') return;
    source.dataset.wysiwygReady = 'true';
    source.classList.add('wysiwyg-source');

    const wrapper = document.createElement('div');
    wrapper.className = 'wysiwyg';
    const toolbar = document.createElement('div');
    toolbar.className = 'wysiwyg-toolbar';
    toolbar.setAttribute('role', 'toolbar');
    toolbar.setAttribute('aria-label', 'Text formatting');
    toolbar.innerHTML = `
        <button type="button" data-rich-command="formatBlock" data-rich-value="p" title="Paragraph">P</button>
        <button type="button" data-rich-command="formatBlock" data-rich-value="h3" title="Heading">H</button>
        <button type="button" data-rich-command="bold" title="Bold"><strong>B</strong></button>
        <button type="button" data-rich-command="italic" title="Italic"><em>I</em></button>
        <button type="button" data-rich-command="insertUnorderedList" title="Bulleted list">• List</button>
        <button type="button" data-rich-command="insertOrderedList" title="Numbered list">1. List</button>
        <button type="button" data-rich-command="formatBlock" data-rich-value="blockquote" title="Quote">❝</button>
        <button type="button" data-rich-command="removeFormat" title="Clear formatting">Clear</button>
        <button type="button" data-rich-command="undo" title="Undo">↶</button>
        <button type="button" data-rich-command="redo" title="Redo">↷</button>`;

    const editor = document.createElement('div');
    editor.className = 'wysiwyg-editor';
    editor.contentEditable = 'true';
    editor.setAttribute('role', 'textbox');
    editor.setAttribute('aria-multiline', 'true');
    editor.setAttribute('aria-label', source.closest('label')?.firstChild?.textContent?.trim() || 'Rich text editor');
    editor.dataset.placeholder = source.getAttribute('placeholder') || 'Write content here…';
    editor.innerHTML = /<[a-z][\s\S]*>/i.test(source.value)
        ? sanitizeRichTextFragment(source.value)
        : plainTextToRichHtml(source.value);

    wrapper.append(toolbar, editor);
    source.after(wrapper);

    const sync = () => {
        const html = sanitizeRichTextFragment(editor.innerHTML).trim();
        source.value = editor.textContent.trim() === '' ? '' : html;
        source.setCustomValidity('');
    };

    toolbar.addEventListener('mousedown', (event) => event.preventDefault());
    toolbar.addEventListener('click', (event) => {
        const button = event.target.closest('[data-rich-command]');
        if (!button) return;
        editor.focus();
        document.execCommand(button.dataset.richCommand, false, button.dataset.richValue || null);
        sync();
    });
    editor.addEventListener('input', sync);
    editor.addEventListener('blur', sync);
    editor.addEventListener('paste', (event) => {
        event.preventDefault();
        document.execCommand('insertText', false, event.clipboardData.getData('text/plain'));
    });
    source.form?.addEventListener('submit', (event) => {
        sync();
        if (source.required && source.value === '') {
            event.preventDefault();
            source.setCustomValidity('Please enter this content.');
            editor.focus();
        }
    });
}

document.querySelectorAll('textarea[data-wysiwyg]').forEach(initializeRichTextEditor);
