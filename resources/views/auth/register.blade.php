@extends('layouts.site')

@section('content')

<section class="min-h-[75vh] bg-slate-50 py-12 md:py-20">
    <div class="mx-auto max-w-lg px-4 sm:px-6">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_55px_rgba(15,23,42,0.09)]">
            <div class="h-2 bg-gradient-to-r from-blue-500 via-blue-700 to-violet-600"></div>

            <div class="p-6 sm:p-9">
                <div class="text-center">
                    <p class="text-sm font-extrabold uppercase tracking-[0.16em] text-blue-700">
                        Fresh Learning
                    </p>

                    <h1 class="mt-3 text-3xl font-extrabold text-slate-950">
                        Create your account
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Join Fresh Learning and begin your church
                        membership and discipleship courses.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('register.store') }}"
                    class="mt-8 space-y-5"
                >
                    @csrf

                    <div>
                        <label
                            for="name"
                            class="block text-sm font-extrabold text-slate-800"
                        >
                            Full name
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required
                            autofocus
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('name')
                            <p class="mt-2 text-sm font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

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
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label
                                for="password"
                                class="block text-sm font-extrabold text-slate-800"
                            >
                                Password
                            </label>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                            >

                            @error('password')
                                <p class="mt-2 text-sm font-semibold text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="password_confirmation"
                                class="block text-sm font-extrabold text-slate-800"
                            >
                                Confirm password
                            </label>

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                            >
                        </div>
                    </div>

                    <p class="text-xs leading-5 text-slate-500">
                        Use at least eight characters, including letters
                        and numbers.
                    </p>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-[rgb(var(--brand))] px-5 py-3.5 font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))]"
                    >
                        Create learning account
                    </button>
                </form>

                <p class="mt-7 text-center text-sm text-slate-600">
                    Already have an account?

                    <a
                        href="{{ route('login') }}"
                        class="font-extrabold text-blue-700 hover:text-blue-900"
                    >
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

@endsection