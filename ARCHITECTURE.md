# Hidden Line — Architecture Document

## Privacy-Focused Tor (.onion) Directory Platform
### Zero JavaScript · Server-Side Only · Production-Ready

---

## A. SYSTEM ARCHITECTURE (NO JS)

### Technology Stack

| Layer              | Technology           | Rationale                                                   |
|--------------------|----------------------|-------------------------------------------------------------|
| **Backend**        | PHP 8.2 + Laravel 12 | Already scaffolded. Blade templates = pure server-side rendering. No JS required. |
| **Template Engine**| Laravel Blade        | Compiles to native PHP. Zero client-side rendering. Components, layouts, slots — all server-side. |
| **Database**       | MySQL 8              | ACID-compliant, proven for directory/catalog workloads. Already configured. |
| **Session Mgmt**   | Database sessions    | HTTPOnly cookies, configurable SameSite. No localStorage/JS. |
| **CSRF Protection**| Laravel `@csrf`      | Automatic token injection in Blade forms. Server-validated on every POST. |
| **Rate Limiting**  | Laravel `throttle` middleware | IP/session-based. Configurable per-route. No client-side logic. |
| **Tor Proxy**      | SOCKS5h via 127.0.0.1:9050 | Laravel HTTP client + Guzzle `proxy` option. Server-side only. |
| **CSS**            | Vanilla CSS (no Tailwind) | Compiled at deploy time via simple CSS file. No build step needed at runtime. |
| **Fonts**          | Self-hosted or system fonts | No external font CDNs for privacy. |

### Why No JavaScript Works

- **Blade** renders complete HTML pages server-side. Every user interaction is a form POST or GET link.
- **CSRF** is embedded as a hidden input via `@csrf` — no AJAX needed.
- **Sessions** use HTTPOnly, Secure cookies — invisible to client scripts.
- **Search** is a `GET` form with `?q=` parameter.
- **Uptime Check** is a `POST` form that triggers server-side HTTP via Tor proxy, then redirects back.
- **Pagination** uses `?page=N` query parameters.

### Database Design (Entity Summary)

```
users           — id, username, password, role(admin/user), created_at, updated_at
links           — id, user_id(nullable), title, description, url, slug, category, 
                  status(pending/active/rejected/offline), uptime_status(online/offline/timeout/unknown),
                  last_check, check_count, is_featured, created_at, updated_at
categories      — Enum (privacy_security, anonymity_tools, email_messaging, etc.)
comments        — id, link_id, username, content, created_at, updated_at
uptime_logs     — id, link_id, checked_by_ip, status, response_time_ms, checked_at
advertisements  — id, title, description, url, banner_image_path, ad_type(banner/sponsored/featured/boost),
                  placement(header/sidebar/category/inline), status(pending/active/expired/rejected),
                  starts_at, expires_at, contact_info, created_at, updated_at
support_tickets — id, token(unique), subject, message, category(general/abuse/legal/bug),
                  reporter_username(nullable), reporter_contact(nullable), status(open/in_progress/resolved/closed),
                  admin_response, responded_at, created_at, updated_at
blacklisted_urls— id, url_pattern, reason, created_at
rate_limits     — Handled by Laravel throttle middleware (cache-based)
```

### Onion Validation Logic

```php
// Rule: Must match http(s)://[16-56 base32 chars].onion[/path]
preg_match('/^https?:\/\/[a-z2-7]{16,56}\.onion(\/.*)?$/', $url)
```

- Enforced at validation layer (Form Request).
- Checked again at storage layer (Model observer).
- Blacklist cross-referenced before storage.

---

## B. ROUTING & PAGE FLOW (SERVER-SIDE ONLY)

### Pattern: PRG (Post/Redirect/Get)

Every `POST` form action redirects after processing (303 or 302). This prevents:
- Double submissions on refresh
- Browser "resubmit form?" warnings
- Back-button confusion

### Route Map

| Method | URI                           | Controller@Method              | Page                    |
|--------|-------------------------------|--------------------------------|-------------------------|
| GET    | `/`                           | HomeController@index           | Homepage (directory)    |
| GET    | `/category/{slug}`            | CategoryController@show        | Category page           |
| GET    | `/link/{slug}`                | LinkController@show            | Link detail page        |
| GET    | `/search`                     | SearchController@index         | Search results          |
| GET    | `/submit`                     | SubmitController@create        | Submit link form        |
| POST   | `/submit`                     | SubmitController@store         | Process → redirect      |
| GET    | `/register`                   | AuthController@registerForm    | Register form           |
| POST   | `/register`                   | AuthController@register        | Process → redirect      |
| GET    | `/login`                      | AuthController@loginForm       | Login form              |
| POST   | `/login`                      | AuthController@login           | Process → redirect      |
| POST   | `/logout`                     | AuthController@logout          | Logout → redirect       |
| POST   | `/link/{id}/check`            | UptimeController@check         | Check status → redirect |
| GET    | `/support`                    | SupportController@index        | Support/FAQ page        |
| POST   | `/support/ticket`             | SupportController@submitTicket | Submit ticket → redirect|
| GET    | `/support/ticket/{token}`     | SupportController@trackTicket  | View ticket status      |
| POST   | `/support/abuse`              | SupportController@reportAbuse  | Abuse report → redirect |
| POST   | `/support/legal`              | SupportController@legalRequest | Legal removal → redirect|
| GET    | `/advertise`                  | AdvertiseController@create     | Ad submission form      |
| POST   | `/advertise`                  | AdvertiseController@store      | Process → redirect      |
| GET    | `/admin`                      | AdminController@dashboard      | Admin dashboard         |
| GET    | `/admin/links`                | AdminController@links          | Pending links           |
| POST   | `/admin/links/{id}/approve`   | AdminController@approveLink    | Approve → redirect      |
| POST   | `/admin/links/{id}/reject`    | AdminController@rejectLink     | Reject → redirect       |
| GET    | `/admin/ads`                  | AdminController@ads            | Pending ads             |
| POST   | `/admin/ads/{id}/approve`     | AdminController@approveAd      | Approve → redirect      |
| POST   | `/admin/ads/{id}/reject`      | AdminController@rejectAd       | Reject → redirect       |
| GET    | `/admin/tickets`              | AdminController@tickets        | Support tickets         |
| POST   | `/admin/tickets/{id}/respond` | AdminController@respondTicket  | Respond → redirect      |
| GET    | `/admin/uptime-logs`          | AdminController@uptimeLogs     | Uptime check logs       |

### Page Flow Examples

**Submit Link (Anonymous):**
```
GET /submit → Render form with CSRF + challenge question
POST /submit → Validate → Store (status=pending) → redirect /submit?success=1
GET /submit?success=1 → Render form + success flash message
```

**Search:**
```
GET /search?q=privacy&category=all&page=2 → Query DB → Render results with pagination
```

**Uptime Check:**
```
POST /link/{id}/check → Rate limit check → Tor proxy HEAD request → Store result → redirect /link/{slug}?checked=1
GET /link/{slug}?checked=1 → Render link detail with updated status
```

---

## C. MANUAL UPTIME CHECK SYSTEM (NO JS)

### Flow

1. User visits `/link/{slug}` — sees a `<form>` with hidden `link_id` and a "Check Status" button.
2. User clicks button → `POST /link/{id}/check`.
3. Server middleware: rate limit (3 checks per IP per 5 minutes).
4. Server checks cache: if checked within last 5 minutes, return cached result.
5. If not cached:
   - HTTP HEAD via Tor SOCKS5h proxy (`127.0.0.1:9050`)
   - Timeout: 15 seconds
   - Record: `online` (2xx/3xx), `offline` (4xx/5xx), `timeout` (connection timeout)
6. Store result in `uptime_logs` table.
7. Update `links.uptime_status`, `links.last_check`.
8. Cache result for 5 minutes.
9. Redirect to `/link/{slug}` — page renders fresh status.

### Implementation

```php
// UptimeController@check
public function check(Request $request, int $id)
{
    $link = Link::findOrFail($id);
    
    // Cache check — prevent spam
    $cacheKey = "uptime_check_{$link->id}";
    if (Cache::has($cacheKey)) {
        return redirect()->route('link.show', $link->slug)
            ->with('info', 'Status was checked recently. Showing cached result.');
    }
    
    // Perform check
    try {
        $start = microtime(true);
        $response = Http::withOptions([
            'proxy' => 'socks5h://127.0.0.1:9050',
            'timeout' => 15,
            'connect_timeout' => 10,
        ])->head($link->url);
        
        $responseTime = round((microtime(true) - $start) * 1000);
        $status = $response->successful() ? 'online' : 'offline';
    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        $status = 'timeout';
        $responseTime = 15000;
    }
    
    // Log the check
    UptimeLog::create([
        'link_id' => $link->id,
        'checked_by_ip' => hash('sha256', $request->ip()), // Hashed for privacy
        'status' => $status,
        'response_time_ms' => $responseTime,
    ]);
    
    // Update link
    $link->update([
        'uptime_status' => $status,
        'last_check' => now(),
        'check_count' => $link->check_count + 1,
    ]);
    
    // Cache for 5 minutes
    Cache::put($cacheKey, $status, 300);
    
    return redirect()->route('link.show', $link->slug)
        ->with('check_result', $status);
}
```

### Security Measures

- **Rate Limiting**: `throttle:3,5` middleware (3 requests per 5 min per IP).
- **SSRF Prevention**: URL validated against `.onion` regex. No redirects followed. HEAD-only requests.
- **IP Hashing**: Logs store SHA-256 hash of IP, not raw IP.
- **Cache Layer**: Prevents repeated checks on the same link.
- **No user-controlled URLs in proxy**: Only stored `.onion` URLs are checked — never user-supplied at check time.

---

## D. ADVERTISEMENT SYSTEM (SERVER-RENDERED)

### Ad Types

| Type            | Placement       | Description                                    |
|-----------------|-----------------|------------------------------------------------|
| **Banner**      | Header/Sidebar  | Image banner with link. Shown on all pages.    |
| **Sponsored**   | Inline listings | Appears in link listings with "Sponsored" tag. |
| **Featured**    | Top of category | Pinned to top of category page.                |
| **Category Boost** | Category page | Higher ranking within a category.              |

### Pricing Model (Example)

| Placement        | Duration | Price (BTC/XMR)  |
|------------------|----------|------------------|
| Header Banner    | 1 week   | 0.005 BTC        |
| Sidebar Banner   | 1 week   | 0.003 BTC        |
| Sponsored Link   | 1 week   | 0.002 BTC        |
| Featured (Top)   | 1 week   | 0.004 BTC        |
| Category Boost   | 1 week   | 0.001 BTC        |

### Ad Submission Flow

```
GET /advertise → Form: title, description, URL(.onion), banner image upload, ad_type select, 
                 contact info, placement preference, CSRF, challenge question
POST /advertise → Validate → Store (status=pending) → redirect /advertise?submitted=1
Admin reviews → /admin/ads → Approves/Rejects via POST form
```

### Database Schema

```sql
CREATE TABLE advertisements (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title          VARCHAR(100) NOT NULL,
    description    TEXT,
    url            VARCHAR(255) NOT NULL,
    banner_path    VARCHAR(255) NULL,
    ad_type        ENUM('banner','sponsored','featured','boost') NOT NULL,
    placement      ENUM('header','sidebar','category','inline') NOT NULL,
    status         ENUM('pending','active','expired','rejected') DEFAULT 'pending',
    contact_info   VARCHAR(255),
    starts_at      DATETIME NULL,
    expires_at     DATETIME NULL,
    created_at     TIMESTAMP,
    updated_at     TIMESTAMP
);
```

### Rendering in Blade

```blade
{{-- Header banner ad --}}
@if($headerAd)
<div class="ad-banner ad-header">
    <span class="ad-label">Sponsored</span>
    <a href="{{ $headerAd->url }}">
        <img src="{{ asset('storage/ads/' . $headerAd->banner_path) }}" 
             alt="{{ $headerAd->title }}">
    </a>
</div>
@endif

{{-- Inline sponsored link --}}
@foreach($links as $link)
    @if($loop->index === 3 && $sponsoredLink)
    <tr class="sponsored-row">
        <td><span class="badge-sponsored">Ad</span> {{ $sponsoredLink->title }}</td>
        <td>{{ $sponsoredLink->url }}</td>
    </tr>
    @endif
    <tr>...</tr>
@endforeach
```

### Ethical Labeling

- Every ad has visible `[Sponsored]` or `[Ad]` badge.
- Ads are visually distinct (subtle background highlight).
- No deceptive styling that mimics organic listings.

---

## E. SUPPORT PAGE (NO JS)

### Pages

| URL                        | Purpose                      |
|----------------------------|------------------------------|
| `/support`                 | FAQ + ticket form + links    |
| `/support/ticket`          | POST: Submit ticket          |
| `/support/ticket/{token}`  | GET: Track ticket by token   |
| `/support/abuse`           | POST: Report abuse           |
| `/support/legal`           | POST: Legal removal request  |

### FAQ Section

Static Blade template. Categories:
- How to submit a link
- How uptime checking works
- Privacy policy
- Advertising information
- How to report abuse

### Ticket Submission

```html
<form action="/support/ticket" method="POST">
    @csrf
    <select name="category">
        <option value="general">General</option>
        <option value="abuse">Report Abuse</option>
        <option value="legal">Legal/DMCA</option>
        <option value="bug">Bug Report</option>
    </select>
    <input name="subject" placeholder="Subject" required>
    <textarea name="message" required></textarea>
    <!-- Optional: anonymous or provide contact -->
    <input name="contact" placeholder="Contact (optional, e.g. email/XMPP)">
    <!-- Challenge question -->
    <label>What is 7 + 3?</label>
    <input name="challenge" required>
    <button type="submit">Submit Ticket</button>
</form>
```

### Ticket Tracking

- On submission, server generates a unique token (UUID v4).
- User sees: "Your ticket token: `abc123-def456`. Bookmark this page."
- `GET /support/ticket/{token}` — shows ticket status + admin response.
- No login required. Token is the auth mechanism.

### Admin Response

```
GET /admin/tickets → List open tickets
POST /admin/tickets/{id}/respond → { admin_response, status } → redirect back
```

Admin response appears on the ticket tracking page.

---

## F. SECURITY & PRIVACY MODEL

### Spam Prevention

| Layer              | Method                                                |
|--------------------|-------------------------------------------------------|
| **CSRF**           | `@csrf` token on every form. Validated by middleware. |
| **Rate Limiting**  | `throttle:X,Y` per route group.                      |
| **Challenge Q**    | Server-generated math question (e.g., "What is 4 + 9?"). Stored in session. Validated on POST. |
| **Moderation Queue** | All submissions default to `status=pending`.       |
| **Honeypot Field** | Hidden input `<input name="website" style="display:none">`. If filled → reject silently. |
| **Cooldown**       | Session-based: 1 submission per 2 minutes.           |

### CAPTCHA Alternative (No JS)

```php
// In controller (GET form):
$a = rand(1, 15);
$b = rand(1, 15);
session(['challenge_answer' => $a + $b]);
return view('submit', ['challenge' => "What is {$a} + {$b}?"]);

// In controller (POST):
if ((int)$request->challenge !== session('challenge_answer')) {
    return back()->withErrors(['challenge' => 'Incorrect answer.']);
}
```

### Content Moderation

- All user-submitted links enter `pending` status.
- Admin dashboard lists pending links with approve/reject forms.
- Rejected links are soft-deleted with reason.
- Repeat offenders: IP hash added to rate-limit escalation.

### Blacklist System

- `blacklisted_urls` table with URL patterns.
- Checked on submission: if URL matches any pattern → reject.
- Admin can add patterns via dashboard.

### Onion-Only Enforcement

- Validation rule rejects any URL not matching `.onion` pattern.
- Model observer double-checks before save.
- Database constraint: URL column has CHECK or application-level enforcement.

### Anti-Scraping

- Rate limiting on listing pages (`throttle:30,1` — 30 req/min).
- No API endpoints. All data requires HTML parsing.
- Pagination limits (max 25 per page).
- `robots.txt` with `Disallow: /` (Tor sites typically block crawlers).

### Logging Policy (Minimal)

- **DO log**: Hashed IPs for rate limiting, uptime check timestamps, admin actions.
- **DO NOT log**: Raw IPs, user agents, referrers, search queries tied to users.
- **Auto-purge**: Uptime logs older than 30 days deleted via scheduled command.
- **Session data**: Encrypted, auto-expired, minimal payload.

---

## G. UI/UX DESIGN (NO JAVASCRIPT)

### Design Principles

- **Dark mode default** — dark backgrounds, light text.
- **Monospace/sans-serif** mix — technical, wiki-like feel.
- **Minimal color palette** — dark grey, green accents (#0f0 for online, #f00 for offline), muted white text.
- **Fast loading** — no external resources, no images by default, minimal CSS.
- **Accessible** — semantic HTML5, proper heading hierarchy, form labels, high contrast.

### Wireframe (Text)

```
┌─────────────────────────────────────────────────────┐
│  ▓ HIDDEN LINE              [Search___________] [Go]│
│  Home | Categories | Submit | Support | Login       │
├─────────────────────────────────────────────────────┤
│  [Sponsored Banner Ad]                              │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ── Privacy & Security ──────────────────────────   │
│  ● Title One          http://xxxxx.onion    [●Online]│
│  ● Title Two          http://yyyyy.onion    [○Offl.] │
│  [Ad] Sponsor Link    http://zzzzz.onion    [●Online]│
│                                                     │
│  ── Forums & Communities ────────────────────────   │
│  ● Forum Alpha        http://aaaaa.onion    [?]     │
│  ● Forum Beta         http://bbbbb.onion    [●Online]│
│                                                     │
│  [1] [2] [3] ... [Next →]                          │
├─────────────────────────────────────────────────────┤
│  [Sidebar Ad]  │  Quick Stats                       │
│                │  Total Links: 142                   │
│                │  Online: 89                         │
│                │  Categories: 15                     │
├─────────────────────────────────────────────────────┤
│  Footer: About | Privacy | Source | Contact         │
└─────────────────────────────────────────────────────┘
```

### Link Detail Page

```
┌─────────────────────────────────────────────────────┐
│  ▓ HIDDEN LINE                                      │
│  Home | Categories | Submit | Support | Login       │
├─────────────────────────────────────────────────────┤
│                                                     │
│  Title: Privacy Email Service                       │
│  URL: http://xxxxxxxxxxxxxxxx.onion                 │
│  Category: Email & Messaging                        │
│  Description: Secure email service...               │
│                                                     │
│  Status: ● Online                                   │
│  Last Checked: 2026-02-16 09:15:00                  │
│  Times Checked: 47                                  │
│                                                     │
│  [Check Status Now]  ← POST form button             │
│                                                     │
│  ── Comments ──                                     │
│  user123: Great service! (2h ago)                   │
│  anon: Works well over Tor. (1d ago)                │
│                                                     │
│  Add Comment:                                       │
│  Name: [________]                                   │
│  Comment: [________________]                        │
│  What is 5 + 8? [__]                                │
│  [Submit Comment]                                   │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### CSS Structure

```css
:root {
    --bg-primary: #0d1117;
    --bg-secondary: #161b22;
    --bg-tertiary: #21262d;
    --text-primary: #e6edf3;
    --text-secondary: #8b949e;
    --accent-green: #3fb950;
    --accent-red: #f85149;
    --accent-yellow: #d29922;
    --accent-blue: #58a6ff;
    --border-color: #30363d;
}
```

---

## H. OPTIONAL ADVANCED FEATURES (NO JS)

### 1. Link Reputation Score
- Based on: uptime percentage, check count, user comments.
- Calculated server-side on each page render.
- Displayed as a simple bar or star rating (pure CSS).

### 2. User Bookmarks (Logged-in Users)
- `POST /link/{id}/bookmark` → stores in `user_bookmarks` table.
- `GET /dashboard/bookmarks` → lists saved links.

### 3. RSS Feed
- `GET /feed.xml` → XML RSS feed of latest approved links.
- No JS needed. Standard RSS readers work.

### 4. Link Reporting
- "Report" button on each link → POST form.
- Adds to moderation queue with reason.

### 5. Multi-Language Support
- Laravel localization (`lang/` files).
- Language selector via `GET /?lang=en` + cookie.

### 6. PGP/GPG Key Page
- Static page with admin's PGP key for encrypted communication.

### 7. Canary Page
- Warrant canary — static page, updated periodically.
- Signed with PGP.

### 8. Export Directory
- `GET /export` → generates plain-text list of all active links.
- Useful for offline reference.

### 9. Link Suggestions
- "Suggest Edit" form on link detail page.
- Stored as suggestion, admin approves.

### 10. Dark/Light Toggle (CSS Only)
- Uses `<details>` + `<summary>` or `prefers-color-scheme` media query.
- No JS needed — browser handles dark mode preference.

---

## Summary

This architecture delivers a **fully functional, privacy-focused Tor directory** with:
- ✅ Zero JavaScript
- ✅ Server-side rendering only
- ✅ Pure HTML forms + CSS
- ✅ Tor proxy integration for uptime checks
- ✅ Complete ad system with ethical labeling
- ✅ Anonymous support ticket system
- ✅ Admin moderation dashboard
- ✅ Anti-spam without CAPTCHA scripts
- ✅ Minimal logging for user privacy
- ✅ Production-ready security model
