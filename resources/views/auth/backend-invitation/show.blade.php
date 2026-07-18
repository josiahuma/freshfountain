<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set up your Fresh Fountain account</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #f6f7fb; color: #111827; }
        .card { width: 100%; max-width: 520px; background: #fff; border: 1px solid #e5e7eb; border-radius: 18px; padding: 32px; box-shadow: 0 20px 45px rgba(15, 23, 42, .08); }
        h1 { margin: 0 0 8px; font-size: 28px; }
        p { color: #4b5563; line-height: 1.6; }
        label { display: block; margin: 18px 0 7px; font-weight: 600; }
        input { width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: 12px 14px; font: inherit; }
        input:focus { outline: 3px solid rgba(37, 99, 235, .15); border-color: #2563eb; }
        button { width: 100%; margin-top: 24px; border: 0; border-radius: 10px; padding: 13px 16px; background: #1d4ed8; color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        .error { margin-top: 8px; color: #b91c1c; font-size: 14px; }
        .email { font-weight: 700; color: #111827; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Set up your account</h1>
        <p>
            Create a secure password for
            <span class="email">{{ $invitation->user->email }}</span>
            to access the Fresh Fountain administration hub.
        </p>

        <form method="POST" action="{{ route('backend-invitation.update', ['token' => $token]) }}">
            @csrf

            <label for="password">New password</label>
            <input id="password" name="password" type="password" required minlength="10" autocomplete="new-password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="10" autocomplete="new-password">

            <button type="submit">Activate account</button>
        </form>
    </main>
</body>
</html>
