@extends('layouts.admin')
@section('title', 'Enquiries')
@section('content')
<div class="page-heading"><div><p class="eyebrow">CONTACT INBOX</p><h1>Enquiries</h1><p class="muted">Review messages submitted through the public contact form and track your response.</p></div><a class="button button-outline" href="{{ route('contact') }}" target="_blank" rel="noopener">View contact page ↗</a></div>
<div class="stats-grid">
    <article class="stat-card"><div><span>All enquiries</span><span class="stat-icon">✉</span></div><strong>{{ $counts['all'] }}</strong><small>Saved contact submissions</small></article>
    <article class="stat-card"><div><span>New</span><span class="stat-icon">●</span></div><strong>{{ $counts['new'] }}</strong><small>Waiting to be reviewed</small></article>
    <article class="stat-card"><div><span>Mail failed</span><span class="stat-icon">!</span></div><strong>{{ $counts['mail_failed'] }}</strong><small>Saved but notification failed</small></article>
</div>
<section class="panel">
    <div class="panel-heading"><div><h2>Contact submissions</h2><p class="muted">{{ $enquiries->total() }} matching {{ Str::plural('enquiry', $enquiries->total()) }}</p></div></div>
    <form class="filter-bar enquiry-filter-bar" method="get">
        <label class="enquiry-search-filter"><span>Search enquiries</span><input type="search" name="search" value="{{ $search }}" placeholder="Name, email, interest, or message"></label>
        <label><span>Enquiry status</span><select name="status"><option value="">All statuses</option>@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>@endforeach</select></label>
        <label><span>Email delivery</span><select name="mail"><option value="">All email delivery</option><option value="sent" @selected($mail === 'sent')>Sent</option><option value="failed" @selected($mail === 'failed')>Failed</option></select></label>
        <div class="enquiry-filter-actions"><button class="button" type="submit">Apply filters</button>@if($search !== '' || $status !== '' || $mail !== '')<a class="button button-outline" href="{{ route('admin.enquiries.index') }}">Clear filters</a>@endif</div>
    </form>
    @if($enquiries->isEmpty())
        <div class="empty-state"><div class="empty-icon">✉</div><h3>No enquiries found</h3><p>New contact-form submissions will appear here.</p></div>
    @else
        <div class="table-wrap"><table><thead><tr><th>Sender</th><th>Enquiry</th><th>Received</th><th>Email</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody>
        @foreach($enquiries as $enquiry)
            <tr><td><span class="table-title">{{ $enquiry->name }}</span><small>{{ $enquiry->email }}@if($enquiry->phone) · {{ $enquiry->phone }}@endif</small></td><td>{{ $enquiry->enquiry_type_label }}<small>{{ $enquiry->interest ?: Str::limit($enquiry->message, 65) }}</small></td><td>{{ $enquiry->created_at->format('d M Y') }}<small>{{ $enquiry->created_at->format('g:i A') }}</small></td><td><span class="badge {{ $enquiry->emailed_at ? 'badge-published' : 'badge-draft' }}">{{ $enquiry->emailed_at ? 'Sent' : ($enquiry->mail_error ? 'Failed' : 'Pending') }}</span></td><td><span class="badge {{ $enquiry->status === 'new' ? 'badge-draft' : 'badge-published' }}">{{ $enquiry->statusLabel() }}</span></td><td><div class="table-actions"><a href="{{ route('admin.enquiries.show', $enquiry) }}">Open</a><form method="post" action="{{ route('admin.enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry permanently?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Delete</button></form></div></td></tr>
        @endforeach
        </tbody></table></div>
        @if($enquiries->hasPages())<div class="pagination">{{ $enquiries->links() }}</div>@endif
    @endif
</section>
@endsection
