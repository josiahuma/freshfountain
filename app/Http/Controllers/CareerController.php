<?php

namespace App\Http\Controllers;

use App\Mail\JobApplicationSubmitted;
use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CareerController extends Controller
{
    public function index()
    {
        $jobs = JobListing::query()
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('careers.index', compact('jobs'));
    }

    public function show(string $slug)
    {
        $job = JobListing::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('careers.show', compact('job'));
    }

    public function apply(string $slug)
    {
        $job = JobListing::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $startStep = 1;

        if (session()->has('errors')) {
            $errorKeys = array_keys(session('errors')->getMessages());

            // Step mapping (Child form currently)
            $step1 = ['full_name','email','phone','ni_number','address'];
            $step2 = ['has_driving_licence','has_car','other_skills'];
            $step3 = ['declaration_signature','declaration_date'];

            if (count(array_intersect($errorKeys, $step3))) {
                $startStep = 3;
            } elseif (count(array_intersect($errorKeys, $step2))) {
                $startStep = 2;
            } else {
                $startStep = 1;
            }
        }

        return view('careers.apply', compact('job', 'startStep'));
    }


    public function submit(Request $request, string $slug)
    {
        $job = JobListing::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

       // Validate only the important fields (still no DB changes)
        $validated = $request->validate([
            'full_name' => ['required','string','max:200'],
            'email'     => ['required','email','max:255'],
            'phone'     => ['required','string','max:50'],

            'dob'       => ['nullable','date'],
            'ni_number' => ['nullable','string','max:50'],
            'address'   => ['nullable','string','max:1000'],

            'right_to_work'   => ['required','in:yes,no'],
            'dbs_status'      => ['nullable','in:none,in_date,on_update_service,expired'],
            'care_experience' => ['required','in:none,lt_1,1_2,3_5,5_plus'],
            'preferred_role'  => ['nullable','in:care_assistant,senior_carer,support_worker'],
            'availability'    => ['required','in:full_time,part_time,bank,night'],
            'start_date'      => ['nullable','date'],
            'why_role'        => ['nullable','string','max:3000'],

            'ref1_name'         => ['required','string','max:150'],
            'ref1_relationship' => ['nullable','string','max:150'],
            'ref1_phone'        => ['nullable','string','max:50'],
            'ref1_email'        => ['nullable','email','max:255'],

            'declare_truth'       => ['accepted'],
            'declare_safeguarding'=> ['accepted'],
            'signature'           => ['required','string','max:255'],
        ]);

        $answers = $request->except(['_token', '_wizard_step']);

        $application = JobApplication::create([
            'job_listing_id' => $job->id,
            'full_name'      => $validated['full_name'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'],
            'status'         => 'new',
            'answers'        => $answers,
            'submitted_at'   => now(),
        ]);


        Mail::to('webmaster@pandtcare.co.uk')->send(new JobApplicationSubmitted($application));

        return redirect()->route('careers.success', $job->slug);
    }


    public function success(string $slug)
    {
        $job = JobListing::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('careers.success', compact('job'));
    }
}
