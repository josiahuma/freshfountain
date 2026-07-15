@extends('layouts.site')

@section('content')

<section class="min-h-[75vh] bg-slate-50 py-12 md:py-20">
    <div class="mx-auto max-w-md px-4 sm:px-6">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_55px_rgba(15,23,42,0.09)]">
            <div class="h-2 bg-gradient-to-r from-blue-500 via-blue-700 to-violet-600"></div>

            <div class="p-6 sm:p-9">
                <div class="text-center">
                    <p class="text-sm font-extrabold uppercase tracking-[0.16em] text-blue-700">
                        Fresh Learning
                    </p>

                    <h1 class="mt-3 text-3xl font-extrabold text-slate-950">
                        Welcome back
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Sign in to continue your membership,
                        baptismal or training course.
                    </p>
                </div>

                @if(session('success'))
                    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                    class="mt-8 space-y-5"
                >
                    @csrf

                    <div>
                        <label
                            for="email"
                            class="block text-sm font-extrabold text-slate-800"
                        >
                            Email address
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                            autofocus
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-4">
                            <label
                                for="password"
                                class="block text-sm font-extrabold text-slate-800"
                            >
                                Password
                            </label>
                        </div>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('password')
                            <p class="mt-2 text-sm font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600"
                        >

                        Keep me signed in
                    </label>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-[rgb(var(--brand))] px-5 py-3.5 font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))]"
                    >
                        Sign in
                    </button>
                </form>

                <p class="mt-7 text-center text-sm text-slate-600">
                    Don’t have a learning account?

                    <a
                        href="{{ route('register') }}"
                        class="font-extrabold text-blue-700 hover:text-blue-900"
                    >
                        Create one
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

@endsection