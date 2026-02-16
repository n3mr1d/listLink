<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HoneypotMiddleware
{
    /**
     * Reject submissions that fill in the honeypot hidden field.
     * Bots tend to fill all form fields — this catches them.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->filled('website_url_hp')) {
            // Silently reject — don't reveal the trap
            return redirect()->back()->with('success', 'Submitted successfully.');
        }

        return $next($request);
    }
}
