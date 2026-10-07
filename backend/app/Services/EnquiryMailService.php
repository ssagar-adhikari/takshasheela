<?php

namespace App\Services;

use App\Mail\ContactEnquiry;
use App\Models\Enquiry;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class EnquiryMailService
{
    public function send(Enquiry $enquiry): bool
    {
        $recipient = Setting::where('key', 'enquiry_email')->value('value')
            ?: Setting::where('key', 'contact_email')->value('value')
            ?: config('site.defaults.contact_email');
        $siteName = Setting::where('key', 'site_name')->value('value')
            ?: config('site.defaults.site_name');

        try {
            Mail::to($recipient)->send(new ContactEnquiry($enquiry, $siteName));
            $enquiry->update(['emailed_at' => now(), 'mail_error' => null]);

            return true;
        } catch (Throwable $exception) {
            report($exception);
            $enquiry->update([
                'emailed_at' => null,
                'mail_error' => Str::limit($exception->getMessage(), 2000, ''),
            ]);

            return false;
        }
    }
}
