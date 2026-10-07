<div class="image-picker" data-image-picker>
    <span class="image-picker-label">{{ $label }}</span>
    <div class="image-picker-body">
        <img class="image-picker-preview" data-image-preview @if($path) src="{{ $section->imageUrl($path) }}" @else hidden @endif alt="Current {{ strtolower($label) }}">
        <div class="image-picker-controls">
            <label>Choose image from device<input type="file" name="{{ $inputName }}" accept="image/jpeg,image/png,image/webp" data-image-input><small>JPG, PNG, or WebP. Maximum 2 MB and 6000 × 6000 pixels per image.</small></label>
            <p class="image-picker-feedback muted" data-image-feedback aria-live="polite"></p>
            @if($path)<label class="checkbox"><input type="checkbox" name="{{ $removeName }}" value="1" data-image-remove @checked(old(str_replace(['][', '[', ']'], ['.', '.', ''], $removeName)))> Remove current image</label>@endif
        </div>
    </div>
</div>
