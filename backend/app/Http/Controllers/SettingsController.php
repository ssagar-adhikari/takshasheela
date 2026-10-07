<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', [
            'settings' => collect(config('site.defaults'))->merge(Setting::pluck('value', 'key')),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:150'],
            'legal_name' => ['nullable', 'string', 'max:200'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'business_description' => ['nullable', 'string', 'max:1000'],
            'default_meta_description' => ['nullable', 'string', 'max:500'],
            'logo_alt' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'enquiry_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'alternate_phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:100'],
            'business_hours' => ['nullable', 'string', 'max:1000'],
            'response_time' => ['nullable', 'string', 'max:255'],
            'map_embed_url' => ['nullable', 'url:http,https', 'max:2000'],
            'map_directions_url' => ['nullable', 'url:http,https', 'max:1000'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'facebook_url' => ['nullable', 'url:http,https', 'max:500'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:500'],
            'youtube_url' => ['nullable', 'url:http,https', 'max:500'],
            'linkedin_url' => ['nullable', 'url:http,https', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
            'remove_logo' => ['sometimes', 'boolean'],
        ]);

        $oldLogoPath = Setting::where('key', 'logo_path')->value('value');
        $newLogoPath = null;
        $removeLogo = $request->boolean('remove_logo');

        try {
            if ($request->hasFile('logo')) {
                $newLogoPath = $request->file('logo')->store('site-settings/logo', 'public');
                if (! $newLogoPath) {
                    throw new RuntimeException('The logo could not be stored. Please try again.');
                }
            }

            unset($validated['logo'], $validated['remove_logo']);
            if ($newLogoPath) {
                $validated['logo_path'] = $newLogoPath;
            } elseif ($removeLogo) {
                $validated['logo_path'] = null;
            }

            DB::transaction(function () use ($validated) {
                foreach ($validated as $key => $value) {
                    Setting::updateOrCreate(['key' => $key], ['value' => $value]);
                }
            });
        } catch (Throwable $exception) {
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }
            throw $exception;
        }

        if (($newLogoPath || $removeLogo) && $oldLogoPath && $oldLogoPath !== $newLogoPath) {
            Storage::disk('public')->delete($oldLogoPath);
        }

        return back()->with('status', 'Business and site settings saved.');
    }
}
