<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use App\Services\Access\BackendInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BackendInvitationController extends Controller
{
    public function show(
        string $token,
        BackendInvitationService $service
    ): View {
        $invitation = $service->findUsable($token);

        abort_unless($invitation, 404, 'This invitation is invalid or has expired.');

        return view('auth.backend-invitation.show', [
            'token' => $token,
            'invitation' => $invitation,
        ]);
    }

    public function update(
        Request $request,
        string $token,
        BackendInvitationService $service
    ): RedirectResponse {
        $invitation = $service->findUsable($token);

        abort_unless($invitation, 404, 'This invitation is invalid or has expired.');

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:10',
                'confirmed',
            ],
        ]);

        $user = $service->accept(
            $invitation,
            $validated['password'],
        );

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect('/hub')->with(
            'status',
            'Your account has been activated successfully.'
        );
    }
}
