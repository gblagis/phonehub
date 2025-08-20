<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email',
            'message' => 'required|string|max:3000',
        ]);

        // Dev: MAIL_MAILER=log για να γράφει στο storage/logs/laravel.log
        Mail::raw("From: {$data['name']} <{$data['email']}>\n\n{$data['message']}", function ($m) {
            $m->to(config('mail.from.address'))->subject('PhoneHub Contact');
        });

        return back()->with('success', 'Thanks! We’ll get back to you.');
    }

    /**
     * Μήνυμα προς πωλητή συγκεκριμένης αγγελίας.
     */
    public function listing(Request $r, Listing $listing)
    {
        $data = $r->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email',
            'message' => 'required|string|max:3000',
        ]);

        // Παραλήπτης: email που δήλωσε ο πωλητής στην αγγελία, αλλιώς του λογαριασμού του
        $to = $listing->contact_email ?: optional($listing->user)->email;

        if (!$to) {
            return back()->withErrors(['email' => 'The seller does not have an email available. Please try calling.']);
        }

        $url   = route('listings.show', $listing);
        $title = $listing->title;
        $body  = "Ad: {$title} (#{$listing->id})\nURL: {$url}\n\n".
                 "Message from {$data['name']} <{$data['email']}>:\n\n{$data['message']}\n";

        Mail::raw($body, function ($m) use ($to, $data, $title, $listing) {
            $m->to($to)
              ->replyTo($data['email'], $data['name'])
              ->subject("PhoneHub: New message for your ad (#{$listing->id}) {$title}");
        });

        return back()->with('success', 'Your message has been sent to the seller.');
    }
}
