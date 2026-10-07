<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\ChronicleArticle;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\WellnessOffering;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'administrators' => User::where('role', 'admin')->count(),
            'wellness' => WellnessOffering::count(),
            'accommodations' => Accommodation::count(),
            'products' => Product::count(),
            'chronicles' => ChronicleArticle::count(),
            'enquiries' => Enquiry::count(),
            'newEnquiries' => Enquiry::where('status', 'new')->count(),
            'settings' => Setting::pluck('value', 'key'),
        ]);
    }
}
