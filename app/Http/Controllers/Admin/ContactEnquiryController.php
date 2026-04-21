<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactEnquiry;

class ContactEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = ContactEnquiry::orderBy('is_read', 'asc')->latest()->paginate(15);
        return view('admin.contact-enquiries.index', compact('enquiries'));
    }

    public function show(ContactEnquiry $contact_enquiry)
    {
        if (!$contact_enquiry->is_read) {
            $contact_enquiry->update(['is_read' => true]);
        }
        return view('admin.contact-enquiries.show', compact('contact_enquiry'));
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
}
