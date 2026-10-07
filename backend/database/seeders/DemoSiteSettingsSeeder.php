<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DemoSiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $demoValues = [
            'enquiry_email' => 'enquiries@takshasheela.com',
            'contact_phone' => '+977 1 661 0123',
            'alternate_phone' => '+977 980 123 4567',
            'whatsapp_phone' => '+977 980 123 4567',
            'postal_code' => '44800',
            'business_hours' => "Sunday–Friday: 8:00 AM–6:00 PM\nSaturday: By appointment",
            'registration_number' => 'TAKSHA-DEMO-001',
            'facebook_url' => 'https://www.facebook.com/takshasheela',
            'instagram_url' => 'https://www.instagram.com/takshasheela',
            'youtube_url' => 'https://www.youtube.com/@takshasheela',
            'linkedin_url' => 'https://www.linkedin.com/company/takshasheela',
        ];

        foreach ($demoValues as $key => $value) {
            $setting = Setting::firstOrNew(['key' => $key]);
            if (! $setting->exists || $setting->value === null || $setting->value === '') {
                $setting->value = $value;
                $setting->save();
            }
        }
    }
}
