<article class="team-member-editor" data-team-member>
    <div class="team-member-editor-heading">
        <div><p class="eyebrow">TEAM MEMBER</p><h3 data-member-heading>{{ $block['title'] }}</h3></div>
        <button class="text-danger team-member-remove" type="button" data-remove-team-member>Delete member</button>
    </div>
    <div class="form-columns">
        <label>Role / subtitle<input name="content[blocks][{{ $memberKey }}][eyebrow]" value="{{ $block['eyebrow'] }}" maxlength="150"></label>
        <label>Full name<input name="content[blocks][{{ $memberKey }}][title]" value="{{ $block['title'] }}" maxlength="255" required data-member-name></label>
    </div>
    <label>Biography<textarea data-wysiwyg name="content[blocks][{{ $memberKey }}][body]" rows="4" maxlength="20000">{{ $block['body'] }}</textarea><small>Use the toolbar to format this content.</small></label>
    @include('admin.about.image', ['inputName' => 'block_images['.$memberKey.']', 'removeName' => 'remove_images['.$memberKey.']', 'path' => $block['image'], 'label' => 'Profile image'])
    <label>Image description<input name="content[blocks][{{ $memberKey }}][image_alt]" value="{{ $block['image_alt'] }}" maxlength="255"></label>
</article>
