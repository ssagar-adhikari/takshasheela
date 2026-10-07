<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Services\EnquiryMailService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->query('status');
        $mail = (string) $request->query('mail');
        $search = trim((string) $request->query('search'));
        $enquiries = Enquiry::query()
            ->when(isset(Enquiry::STATUSES[$status]), fn ($query) => $query->where('status', $status))
            ->when($mail === 'sent', fn ($query) => $query->whereNotNull('emailed_at'))
            ->when($mail === 'failed', fn ($query) => $query->whereNull('emailed_at')->whereNotNull('mail_error'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('message', 'like', '%'.$search.'%')
                        ->orWhere('interest', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries' => $enquiries,
            'status' => isset(Enquiry::STATUSES[$status]) ? $status : '',
            'mail' => in_array($mail, ['sent', 'failed'], true) ? $mail : '',
            'search' => $search,
            'statuses' => Enquiry::STATUSES,
            'counts' => [
                'all' => Enquiry::count(),
                'new' => Enquiry::where('status', 'new')->count(),
                'mail_failed' => Enquiry::whereNull('emailed_at')->whereNotNull('mail_error')->count(),
            ],
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        if ($enquiry->status === 'new') {
            $enquiry->update(['status' => 'read']);
        }

        return view('admin.enquiries.show', [
            'enquiry' => $enquiry->fresh(),
            'statuses' => Enquiry::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Enquiry::STATUSES))],
        ]);
        $enquiry->update($validated);

        return back()->with('status', 'Enquiry status updated.');
    }

    public function resend(Enquiry $enquiry, EnquiryMailService $mailService)
    {
        if (! $mailService->send($enquiry)) {
            return back()->withErrors(['mail' => 'The email could not be sent. The enquiry remains saved; check the Gmail SMTP settings and try again.']);
        }

        return back()->with('status', 'Enquiry notification sent to the admin email.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')->with('status', 'Enquiry deleted.');
    }
}
