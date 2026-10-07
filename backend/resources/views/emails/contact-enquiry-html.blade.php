<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>New website enquiry</title></head>
<body style="margin:0;background:#f4f1e8;color:#233127;font-family:Arial,sans-serif;">
<div style="max-width:680px;margin:0 auto;padding:32px 16px;"><div style="background:#fff;border:1px solid #ddd7c7;border-radius:12px;overflow:hidden;">
<div style="background:#264b3a;color:#fff;padding:24px 28px;"><p style="margin:0 0 8px;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;">{{ $siteName }}</p><h1 style="margin:0;font-size:24px;line-height:1.3;">New website enquiry #{{ $enquiry->id }}</h1></div>
<div style="padding:28px;"><table role="presentation" style="width:100%;border-collapse:collapse;font-size:15px;line-height:1.5;">
<tr><td style="width:145px;padding:8px 0;color:#697269;vertical-align:top;">Name</td><td style="padding:8px 0;font-weight:600;">{{ $enquiry->name }}</td></tr>
<tr><td style="padding:8px 0;color:#697269;vertical-align:top;">Email</td><td style="padding:8px 0;"><a href="mailto:{{ $enquiry->email }}" style="color:#264b3a;">{{ $enquiry->email }}</a></td></tr>
<tr><td style="padding:8px 0;color:#697269;vertical-align:top;">Phone</td><td style="padding:8px 0;">{{ $enquiry->phone ?: 'Not provided' }}</td></tr>
<tr><td style="padding:8px 0;color:#697269;vertical-align:top;">Enquiry type</td><td style="padding:8px 0;">{{ $enquiry->enquiry_type_label }}</td></tr>
<tr><td style="padding:8px 0;color:#697269;vertical-align:top;">Specific interest</td><td style="padding:8px 0;">{{ $enquiry->interest ?: 'Not specified' }}</td></tr>
<tr><td style="padding:8px 0;color:#697269;vertical-align:top;">Submitted</td><td style="padding:8px 0;">{{ $enquiry->created_at->format('d M Y, g:i A') }}</td></tr>
</table><div style="margin-top:22px;padding-top:22px;border-top:1px solid #e5e0d4;"><p style="margin:0 0 10px;color:#697269;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Message</p><p style="margin:0;line-height:1.7;white-space:pre-line;">{{ $enquiry->message }}</p></div>
<p style="margin:26px 0 0;padding:16px;background:#f4f1e8;border-radius:8px;font-size:14px;line-height:1.5;">Reply directly to this email to respond to {{ $enquiry->name }}.</p></div></div></div>
</body>
</html>
