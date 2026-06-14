<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Inertia\Inertia;

class ContactEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = ContactEnquiry::orderBy('is_read', 'asc')->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/ContactEnquiries/Index', [
            'enquiries' => $enquiries->through(fn (ContactEnquiry $enquiry) => $this->mapEnquiry($enquiry)),
            'stats' => [
                'total' => ContactEnquiry::count(),
                'unread' => ContactEnquiry::where('is_read', false)->count(),
                'read' => ContactEnquiry::where('is_read', true)->count(),
            ],
        ]);
    }

    public function show(ContactEnquiry $contact_enquiry)
    {
        if (!$contact_enquiry->is_read) {
            $contact_enquiry->update(['is_read' => true]);
        }

        return Inertia::render('Admin/ContactEnquiries/Show', [
            'enquiry' => $this->mapEnquiry($contact_enquiry, true),
        ]);
    }

    public function markAsRead(ContactEnquiry $contact_enquiry)
    {
        $contact_enquiry->update(['is_read' => true]);
        return back()->with('success', 'Enquiry marked as read.');
    }

    public function destroy(ContactEnquiry $contact_enquiry)
    {
        $contact_enquiry->delete();
        return redirect()->route('admin.contact-enquiries.index')->with('success', 'Enquiry deleted successfully.');
    }

    private function mapEnquiry(ContactEnquiry $enquiry, bool $includeMessage = false): array
    {
        $data = [
            'id' => $enquiry->id,
            'name' => $enquiry->name,
            'email' => $enquiry->email,
            'subject' => $enquiry->subject,
            'is_read' => (bool) $enquiry->is_read,
            'created_at' => $enquiry->created_at?->format('d M Y, h:i A'),
            'created_date' => $enquiry->created_at?->format('d M Y'),
            'created_relative' => $enquiry->created_at?->diffForHumans(),
            'email_url' => 'mailto:'.$enquiry->email,
            'actions' => [
                'show' => route('admin.contact-enquiries.show', $enquiry),
                'read' => route('admin.contact-enquiries.read', $enquiry),
                'destroy' => route('admin.contact-enquiries.destroy', $enquiry),
            ],
        ];

        if ($includeMessage) {
            $data['message'] = $enquiry->message;
            $data['reply_url'] = 'mailto:'.$enquiry->email.'?subject='.rawurlencode('RE: '.$enquiry->subject);
        }

        return $data;
    }
}
