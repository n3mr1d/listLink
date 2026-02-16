<?php

namespace App\Rules;

use App\Models\BlacklistedUrl;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UrlFilter implements ValidationRule
{
    /**
     * Validate that the URL is a valid .onion address.
     * Supports both v2 (16 chars) and v3 (56 chars) onion addresses.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Must be a valid .onion URL
        if (!preg_match('/^https?:\/\/[a-z2-7]{16,56}\.onion(\/.*)?$/i', $value)) {
            $fail('The :attribute must be a valid .onion URL (e.g., http://example1234567890.onion).');
            return;
        }

        // Check against blacklist
        $blacklisted = BlacklistedUrl::get();
        foreach ($blacklisted as $entry) {
            if (str_contains($value, $entry->url_pattern)) {
                $fail('This URL has been blacklisted and cannot be submitted.');
                return;
            }
        }
    }
}
