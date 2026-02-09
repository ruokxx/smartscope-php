<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
        }

        if ($request->user()) {
            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }
        }
        else {
            // User not logged in.
            // We need to manually verify based on ID/Hash since EmailVerificationRequest relies on $request->user()
            // However, standard EmailVerificationRequest authorizes based on user().
            // So we might need to login the user or manually check signature.

            // Let's try to find the user by ID from the route.
            $user = \App\Models\User::find($request->route('id'));

            if ($user && hash_equals((string)$request->route('hash'), sha1($user->getEmailForVerification()))) {
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                    event(new Verified($user));
                }
                // Log them in? Or just redirect with message?
                // Let's log them in for smooth UX.
                auth()->login($user);
            }
            else {
                abort(403);
            }
        }

        return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }
}
