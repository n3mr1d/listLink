<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawledEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'source_url',
        'source_domain',
        'page_title',
        'status',
        'source_type',
        'crawl_job_id',
        'exported',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'exported'      => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at'  => 'datetime',
    ];

    /**
     * Validate an email address format.
     */
    public static function isValidEmail(string $email): bool
    {
        // Basic format + filter
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Domain must have valid TLD (≥ 2 chars)
        $parts = explode('@', $email);
        if (count($parts) !== 2) return false;

        $domain = $parts[1];
        $tld    = substr(strrchr($domain, '.'), 1);

        if (strlen($tld) < 2 || strlen($tld) > 12) return false;

        // Skip known disposable/placeholder domains
        $junkDomains = [
            'example.com', 'test.com', 'noreply.com', 'localhost',
            'mailinator.com', 'guerrillamail.com', 'temp-mail.org',
            'yopmail.com', 'trashmail.com', 'fakeinbox.com',
        ];

        if (in_array(strtolower($domain), $junkDomains)) {
            return false;
        }

        // Skip noreply / do-not-reply patterns
        $localPart = strtolower($parts[0]);
        $skipPrefixes = ['noreply', 'no-reply', 'donotreply', 'do-not-reply', 'postmaster', 'mailer-daemon', 'bounce'];
        foreach ($skipPrefixes as $prefix) {
            if (str_starts_with($localPart, $prefix)) return false;
        }

        return true;
    }

    /**
     * Upsert an email: create if new, refresh last_seen_at if existing.
     * Returns ['created' => bool, 'model' => CrawledEmail].
     */
    public static function upsertEmail(
        string $email,
        ?string $sourceUrl = null,
        ?string $pageTitle = null,
        string $sourceType = 'auto_crawl',
        ?string $jobId = null
    ): array {
        $email  = strtolower(trim($email));
        $domain = explode('@', $email)[1] ?? null;

        $existing = static::where('email', $email)->first();

        if ($existing) {
            $existing->update([
                'last_seen_at' => now(),
                // Re-enrich with newer source if more info available
                'source_url'   => $existing->source_url ?? $sourceUrl,
                'page_title'   => $existing->page_title ?? $pageTitle,
            ]);
            return ['created' => false, 'model' => $existing];
        }

        $model = static::create([
            'email'         => $email,
            'source_url'    => $sourceUrl,
            'source_domain' => $domain,
            'page_title'    => $pageTitle ? \Illuminate\Support\Str::limit($pageTitle, 490, '') : null,
            'status'        => 'active',
            'source_type'   => $sourceType,
            'crawl_job_id'  => $jobId,
            'first_seen_at' => now(),
            'last_seen_at'  => now(),
        ]);

        return ['created' => true, 'model' => $model];
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExported($query)
    {
        return $query->where('exported', true);
    }

    public function scopeNotExported($query)
    {
        return $query->where('exported', false);
    }
}
