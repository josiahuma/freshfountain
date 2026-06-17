<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MicrosoftGraphMailer;

class ContactController extends Controller
{
    public function send(Request $request, MicrosoftGraphMailer $mailer)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            'company' => ['nullable', 'string', 'max:255'], // Honeypot
        ]);

        // Honeypot check
        if (!empty($validated['company'] ?? null)) {
            return back()->with('success', 'Thanks! Your message has been sent.');
        }

        $to = config('mail.contact_to', 'admin@gimscare.co.uk');

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'messageText' => $validated['message'],
            'submittedAt' => now()->format('d M Y, H:i'),
            'ip' => $request->ip(),
        ];

        // Admin email
        try {
            $mailer->send(
                $to,
                'New contact form message',
                view('emails.contact-message', $data)->render()
            );
        } catch (\Throwable $e) {
            report($e);
       }
        

        // Confirmation email to sender
        try {
            $mailer->send(
                $validated['email'],
                'We’ve received your message',
                view('emails.contact-received', $data)->render()
            );
        } catch (\Throwable $e) {
            report($e);
       }
        

        return back()->with('success', 'Thanks! Your message has been sent.');
    }
}