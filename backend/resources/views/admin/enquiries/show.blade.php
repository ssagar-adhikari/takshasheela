@extends('layouts.admin')
@section('title', 'Enquiry #'.$enquiry->id)
@section('content')
<div class="page-heading"><div><p class="eyebrow">CONTACT ENQUIRY</p><h1>{{ $enquiry->name }}</h1><p class="muted">Received {{ $enquiry->created_at->format('d M Y, g:i A') }}</p></div><a class="button button-outline" href="{{ route('admin.enquiries.index') }}">← All enquiries</a></div>
<div class="editor-grid enquiry-detail-grid">
    <div class="stack">
        <section class="panel form-panel"><div><p class="eyebrow">MESSAGE</p><h2>{{ $enquiry->enquiry_type_label }}</h2>@if($enquiry->interest)<p class="muted">Specific interest: {{ $enquiry->interest }}</p>@endif</div><div class="enquiry-message">{{ $enquiry->message }}</div></section>
        <section class="panel form-panel"><div><p class="eyebrow">SENDER</p><h2>Contact details</h2></div><dl class="enquiry-details"><div><dt>Name</dt><dd>{{ $enquiry->name }}</dd></div><div><dt>Email</dt><dd><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></dd></div><div><dt>Phone</dt><dd>@if($enquiry->phone)<a href="tel:{{ preg_replace('/[^+0-9]/', '', $enquiry->phone) }}">{{ $enquiry->phone }}</a>@else Not provided @endif</dd></div><div><dt>Consent</dt><dd>{{ $enquiry->consent ? 'Accepted' : 'Not recorded' }}</dd></div></dl></section>
    </div>
    <aside class="stack">
        <section class="panel form-panel"><div><p class="eyebrow">WORKFLOW</p><h2>Enquiry status</h2></div><form method="post" action="{{ route('admin.enquiries.status', $enquiry) }}" class="stack">@csrf @method('PATCH')<label>Status<select name="status">@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected($enquiry->status === $value)>{{ $label }}</option>@endforeach</select></label><button class="button" type="submit">Update status</button></form></section>
        <section class="panel form-panel"><div><p class="eyebrow">ACTIONS</p><h2>Delete enquiry</h2><p class="muted">This permanently removes the saved submission.</p></div><form method="post" action="{{ route('admin.enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry permanently?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Delete enquiry</button></form></section>
    </aside>
</div>
@endsection
