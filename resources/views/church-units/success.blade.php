@extends('layouts.site')

@section('content')
<section class="relative overflow-hidden bg-[rgb(var(--navy))] text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/70 via-slate-950/70 to-black"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 py-20 md:py-28">
        <div class="mx-auto max-w-3xl text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full
                        border border-white/15 bg-white/10 text-4xl">
                ✓
            </div>

            <p class="mt-8 text-sm font-extrabold uppercase tracking-[0.22em] text-blue-300">
                Request received
            </p>

            <h1 class="mt-4 text-4xl font-extrabold leading-tight md:text-6xl">
                Thank you
            </h1>

            <p class="mt-6 text-lg leading-relaxed text-white/75 md:text-xl">
                Your request to join
                <span class="font-extrabold text-white">
                    {{ $churchUnit->name }}
                </span>
                has been received successfully.
            </p>
        </div>
    </div>
</section>

<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="max-w-[1400px] mx-auto px-4 py-14 md:py-20">
        <div class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white p-8 text-center
                    shadow-[0_14px_40px_rgba(15,23,42,0.10)] md:p-12">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-2xl">
                🎉
            </div>

            <h2 class="mt-6 text-3xl font-extrabold text-slate-950">
                What happens next?
            </h2>

            <p class="mt-4 text-lg leading-relaxed text-slate-600">
                The leadership team for {{ $churchUnit->name }} will review your
                request. One of the leaders will contact you using the email address
                or mobile number you provided.
            </p>

            <div class="mt-8 grid gap-4 text-left sm:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 font-extrabold text-blue-800">
                        1
                    </div>

                    <h3 class="mt-4 font-extrabold text-slate-950">
                        Request reviewed
                    </h3>

                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        The unit leadership team receives your request.
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 font-extrabold text-blue-800">
                        2
                    </div>

                    <h3 class="mt-4 font-extrabold text-slate-950">
                        Leader contacts you
                    </h3>

                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        A leader will get in touch to discuss the next step.
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 font-extrabold text-blue-800">
                        3
                    </div>

                    <h3 class="mt-4 font-extrabold text-slate-950">
                        Join the team
                    </h3>

                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        You will receive the information needed to get started.
                    </p>
                </div>
            </div>

            <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
                <a
                    href="{{ route('church-units.show', $churchUnit->slug) }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200
                           bg-white px-6 py-3 font-extrabold text-slate-900
                           transition hover:bg-slate-50"
                >
                    Return to {{ $churchUnit->name }}
                </a>

                <a
                    href="{{ route('church-units.index') }}"
                    class="inline-flex items-center justify-center rounded-2xl
                           bg-[rgb(var(--brand))] px-6 py-3 font-extrabold text-white
                           transition hover:bg-[rgb(var(--brand-dark))]"
                >
                    Explore other units
                </a>
            </div>
        </div>
    </div>
</section>
@endsection