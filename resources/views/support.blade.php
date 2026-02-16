<x-app.layouts title="Support">

    <div class="page-full">
        <div class="page-header">
            <h1>Support Center</h1>
            <p>Get help, report issues, or support the project</p>
        </div>

        {{-- FAQ Section --}}
        <div class="faq-section">
            <h2 style="font-size:1.1rem;margin-bottom:1rem;color:var(--text-primary);">Frequently Asked Questions</h2>

            <details class="faq-item">
                <summary>How do I submit a link?</summary>
                <div class="faq-answer">
                    Visit the <a href="{{ route('submit.create') }}">Submit Link</a> page. Fill in the .onion URL and
                    category. If you are logged in, your link will appear in both the <strong>Tor Directory</strong>
                    (homepage) and the <strong>Search Engine</strong>.
                    <br><br>
                    <span style="color:var(--accent-yellow);font-weight:bold;">⚠ Anonymous submissions</span> will
                    <strong>only</strong> appear in the Search Engine — they will <strong>not</strong> be listed in the
                    Tor Directory homepage. <a href="{{ route('register.form') }}">Create an account</a> to get
                    full directory listing.
                </div>
            </details>

            <details class="faq-item">
                <summary>How does the uptime checker work?</summary>
                <div class="faq-answer">
                    On each link's detail page, there is a "Check Status Now" button. When clicked, our server connects
                    to the .onion address through the Tor network and reports whether the site is online, offline, or
                    timed out.
                    Results are cached for 5 minutes to prevent abuse. The check may take up to 15 seconds.
                </div>
            </details>

            <details class="faq-item">
                <summary>Is this site safe to use?</summary>
                <div class="faq-answer">
                    This site uses zero JavaScript, ensuring no client-side tracking or fingerprinting is possible.
                    Sessions are managed with HTTP-only cookies. We collect minimal data and do not log IP addresses.
                    Always use Tor Browser for maximum privacy.
                </div>
            </details>

            <details class="faq-item">
                <summary>How do I advertise on Hidden Line?</summary>
                <div class="faq-answer">
                    Visit the <a href="{{ route('advertise.create') }}">Advertise</a> page to submit an ad request.
                    We offer banner placements, sponsored links, featured spots, and category boosts.
                    All ads are clearly labeled as "Sponsored" and go through manual approval.
                    <br><br>
                    You can also contact us directly at <a href="mailto:treixnox@protonmail.com"
                        style="color:var(--accent-blue);font-weight:700;">treixnox@protonmail.com</a>
                    for custom deals or if you have any suggestions for the platform.
                </div>
            </details>

            <details class="faq-item">
                <summary>Why isn't my link showing on the homepage?</summary>
                <div class="faq-answer">
                    The Tor Directory homepage only displays links from <strong>registered users</strong>. If you
                    submitted anonymously, your link will only appear in the Search Engine. To get your link on
                    the homepage, <a href="{{ route('register.form') }}">create an account</a> and submit while logged
                    in.
                </div>
            </details>

            <details class="faq-item">
                <summary>Does the crawler auto-fill my link details?</summary>
                <div class="faq-answer">
                    Yes! When you submit a .onion URL, our server-side crawler will attempt to fetch the page through
                    the Tor network and automatically extract the page <strong>title</strong> and
                    <strong>description</strong> (from meta tags). You can still edit these before submitting. The
                    crawler works entirely server-side — no JavaScript required.
                </div>
            </details>
        </div>

        {{-- ═══ Donate Section ═══ --}}
        <div style="margin-bottom:2rem;">
            <h2
                style="font-size:1.1rem;margin-bottom:1rem;color:var(--text-primary);display:flex;align-items:center;gap:0.5rem;">
                <span style="color:var(--accent-red);font-size:1.25rem;">♥</span> Support Hidden Line
            </h2>

            <div class="card" style="overflow:hidden;">
                <div class="card-body"
                    style="background:linear-gradient(to right, #1a1f2e, var(--bg-secondary));border-bottom:1px solid var(--border-color);padding:1rem;">
                    <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.6;">
                        Hidden Line is a free, open-source, privacy-focused project. Running Tor infrastructure,
                        maintaining uptime checkers, and crawling .onion sites costs money. Your donations help
                        keep this service alive and free for everyone.
                    </p>
                </div>

                {{-- Bitcoin --}}
                <div style="padding:1rem;border-bottom:1px solid var(--border-color);">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;">
                        <span
                            style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:6px;font-size:1.25rem;background:rgba(247, 147, 26, 0.15);color:#f7931a;border:1px solid rgba(247, 147, 26, 0.3);">
                            ₿
                        </span>
                        <div>
                            <h3
                                style="font-weight:600;font-size:0.95rem;color:var(--text-primary);margin-bottom:0.1rem;">
                                Bitcoin (BTC)</h3>
                            <p style="font-size:0.75rem;color:var(--text-muted);">On-chain payment</p>
                        </div>
                    </div>
                    <div
                        style="padding:0.6rem;border-radius:4px;background:var(--bg-primary);border:1px solid var(--border-color);font-family:var(--font-mono);font-size:0.8rem;color:var(--accent-cyan);word-break:break-all;">

                        {{ config('Donate.btc') }}
                    </div>
                </div>

                {{-- Monero --}}
                <div style="padding:1rem;">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;">
                        <span
                            style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:6px;font-size:1.25rem;background:rgba(255, 106, 0, 0.15);color:#ff6a00;border:1px solid rgba(255, 106, 0, 0.3);">
                            ⛏
                        </span>
                        <div>
                            <h3
                                style="font-weight:600;font-size:0.95rem;color:var(--text-primary);margin-bottom:0.1rem;">
                                Ethreum (ETH)</h3>
                            <p style="font-size:0.75rem;color:var(--text-muted);">Private & untraceable — preferred</p>
                        </div>
                    </div>
                    <div
                        style="padding:0.6rem;border-radius:4px;background:var(--bg-primary);border:1px solid var(--border-color);font-family:var(--font-mono);font-size:0.8rem;color:var(--accent-cyan);word-break:break-all;">
                        {{ config('Donate.XMR') }}
                    </div>
                </div>
            </div>

            <p style="text-align:center;font-size:0.8rem;color:var(--text-muted);margin-top:1rem;">
                All donations are anonymous. Thank you for keeping the darknet directory alive. ♥
            </p>
        </div>
    </div>

</x-app.layouts>