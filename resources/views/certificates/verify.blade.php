@extends('layouts.site')

@section('content')

<section
    class="relative overflow-hidden py-14 text-white md:py-20
        {{ $isValid
            ? 'bg-emerald-950'
            : 'bg-slate-950'
        }}"
>
    <div
        class="absolute inset-0 bg-gradient-to-br
            {{ $isValid
                ? 'from-emerald-950 via-slate-950 to-blue-950'
                : 'from-slate-950 via-red-950 to-slate-950'
            }}"
    ></div>

    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <div
            class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border border-white/15 bg-white/10 text-4xl"
        >
            {{ $isValid ? '✓' : '×' }}
        </div>

        <p class="mt-7 text-sm font-extrabold uppercase tracking-[0.16em] text-blue-300">
            Certificate verification
        </p>

        <h1 class="mt-4 text-4xl font-extrabold md:text-6xl">
            @if($isValid)
                Certificate verified
            @elseif($certificate)
                Certificate revoked
            @else
                Certificate not found
            @endif
        </h1>

        <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-slate-300">
            @if($isValid)
                This is an authentic Fresh Fountain
                Christian Network certificate.
            @elseif($certificate)
                This certificate is no longer valid.
            @else
                We could not locate a certificate
                matching this verification code.
            @endif
        </p>
    </div>
</section>

<section class="min-h-[50vh] bg-slate-100 py-10 md:py-16">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        @if($certificate)
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-6 md:p-8">
                    <p class="text-xs font-extrabold uppercase tracking-[0.15em] text-blue-700">
                        Certificate
                    </p>

                    <h2 class="mt-3 text-2xl font-extrabold text-slate-950">
                        {{ $certificate->course_title }}
                    </h2>
                </div>

                <div class="grid gap-5 p-6 sm:grid-cols-2 md:p-8">
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                            Awarded to
                        </p>

                        <p class="mt-2 font-extrabold text-slate-950">
                            {{ $certificate->student_name }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                            Certificate number
                        </p>

                        <p class="mt-2 break-all font-extrabold text-slate-950">
                            {{ $certificate->certificate_number }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                            Date issued
                        </p>

                        <p class="mt-2 font-extrabold text-slate-950">
                            {{ $certificate->issued_at->format('j F Y') }}
                        </p>
                    </div>

                    <div class="rounded-2xl p-5
                        {{ $isValid
                            ? 'bg-emerald-50'
                            : 'bg-red-50'
                        }}"
                    >
                        <p class="text-xs font-extrabold uppercase tracking-wide
                            {{ $isValid
                                ? 'text-emerald-700'
                                : 'text-red-700'
                            }}"
                        >
                            Status
                        </p>

                        <p class="mt-2 font-extrabold
                            {{ $isValid
                                ? 'text-emerald-950'
                                : 'text-red-950'
                            }}"
                        >
                            {{ $isValid ? 'Valid' : 'Revoked' }}
                        </p>
                    </div>

                    @if($certificate->isRevoked() && $certificate->revocation_reason)
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 sm:col-span-2">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-red-700">
                                Revocation reason
                            </p>

                            <p class="mt-3 leading-7 text-red-950">
                                {{ $certificate->revocation_reason }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="rounded-3xl border border-red-200 bg-white p-8 text-center shadow-sm">
                <h2 class="text-2xl font-extrabold text-slate-950">
                    Invalid verification code
                </h2>

                <p class="mt-3 text-slate-600">
                    Check that the complete verification
                    address was entered correctly.
                </p>
            </div>
        @endif
    </div>
</section>

@endsection