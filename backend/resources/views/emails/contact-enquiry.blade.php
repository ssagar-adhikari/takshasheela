New enquiry #{{ $enquiry->id }} from the {{ $siteName }} website

Name: {{ $enquiry->name }}
Email: {{ $enquiry->email }}
Phone: {{ $enquiry->phone ?: 'Not provided' }}
Enquiry type: {{ $enquiry->enquiry_type_label }}
Specific interest: {{ $enquiry->interest ?: 'Not specified' }}
Submitted: {{ $enquiry->created_at->format('d M Y, g:i A') }}

Message:
{{ $enquiry->message }}

Reply directly to this email to respond to {{ $enquiry->name }} at {{ $enquiry->email }}.
