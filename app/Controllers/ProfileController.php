<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

/**
 * Auth profile actions that must work even with an idle/expired session.
 *
 * Logout is registered outside the session auth filter and exempt from CSRF
 * (.cursorrules §4.3 / CONTEXT.md §3).
 */
class ProfileController extends BaseController
{
    /**
     * Idempotent logout: clears residual session data even if already logged out,
     * then redirects to /cp without throwing.
     */
    public function logout(): RedirectResponse
    {
        if (auth()->loggedIn()) {
            auth()->logout();
        } else {
            session()->destroy();
        }

        return redirect()->route('login');
    }
}
