@php
    $primaryAddress = trim((string) ($siteSettings['address'] ?? ''));
    $city = trim((string) ($siteSettings['city'] ?? ''));
    $postalCode = trim((string) ($siteSettings['postal_code'] ?? ''));
    $country = trim((string) ($siteSettings['country'] ?? ''));
    $cityLine = collect([
        $city !== '' && stripos($primaryAddress, $city) === false ? $city : null,
        $postalCode !== '' && stripos($primaryAddress, $postalCode) === false ? $postalCode : null,
    ])->filter()->implode(' ');
    $countryLine = $country !== '' && stripos($primaryAddress, $country) === false ? $country : null;
@endphp
@if($primaryAddress !== ''){!! nl2br(e($primaryAddress)) !!}@endif
@if($cityLine !== '')<br>{{ $cityLine }}@endif
@if($countryLine)<br>{{ $countryLine }}@endif
