<x-app.layouts title="Support Center">

    <div style="max-width:760px;margin:0 auto;padding:1.5rem 0 3rem;">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:2rem;">
            <h1 style="font-size:1.75rem;font-weight:900;color:#fff;margin:0 0 .4rem;letter-spacing:-.02em;">Support
                Center</h1>
            <p style="color:var(--color-gh-dim);font-size:.9rem;margin:0;">Get help, report issues, or support the
                project</p>
        </div>

        {{-- FAQ --}}
        <section style="margin-bottom:2rem;">
            <h2
                style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);margin:0 0 .75rem;">
                ❓ Frequently Asked Questions
            </h2>
            <div style="display:flex;flex-direction:column;gap:.5rem;">

                @foreach([
                        [
                            'q' => 'How do I submit a link?',
                            'a' => 'Visit the <a href="' . route('submit.create') . '" style="color:var(--color-gh-accent);">Submit Link</a> page. If logged in, your link appears in the Directory and Search Engine. Anonymous submissions only appear in the Search Engine. <a href="' . route('register.form') . '" style="color:var(--color-gh-accent);">Create an account</a> for full listing.',
                        ],
                        [
                            'q' => 'How does the uptime checker work?',
                            'a' => 'On each link\'s detail page, click "Check Status Now". Our server connects through Tor and reports online/offline status. Results are cached 5 minutes. The check may take up to 15 seconds.',
                        ],
                        [
                            'q' => 'Is this site safe to use?',
                            'a' => 'Sessions are managed with HTTP-only cookies. We collect minimal data and do not log IP addresses. Always use Tor Browser for maximum privacy.',
                        ],
                        [
                            'q' => 'How do I advertise on ' . config('app.name') . ' ?',
                            'a' => 'Visit the <a href="' . route('advertise.create') . '" style="color:var(--color-gh-accent);">Advertise</a> page to submit an ad request. We offer banner placements, sponsored links, and featured spots. Contact <a href="mailto:' . config('site.contact_email') . '" style="color:var(--color-gh-accent);">' . config('site.contact_email') . '</a> for custom deals.',
                        ],
                    ] as $faq)

                        <details style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                            <summary style="display:flex;align-items:center;justify-content:space-between;padding:.9rem 1rem;cursor:pointer;list-style:none;color:#fff;font-size:.85rem;font-weight:600;">
                                {{ $faq['q'] }}
                                <span style="color:var(--color-gh-dim);font-size:.7rem;flex-shrink:0;margin-left:.75rem;">▼</span>
                            </summary>

                                               <div style="padding:.75rem 1rem;border-top:1px solid var(--color-gh-border);font-size:.8rem;color:var(--color-gh-dim);line-height:1.7;">
                                {!! $faq['a'] !!}
                            </div>
                        </details>
                @endforeach

            </div>
        </section>

                           
                            
        {{-- Donate --}}
        <section style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--color-gh-border);">
                <h2 style="color:#fff;font-size:.95rem;font-weight:800;margin:0 0 .35rem;display:flex;align-items:center;gap:.4rem;">
                    <spa
              n          style="color:#f87171;">♥</span> Support {{ config('app.name') }}
                </h2>
                <p style="color:var(--color-gh-dim);font-size:.78rem;line-height:1.6;margin:0;">
                    {{ config('app.name') }} is a free, open-source project. Maintaining Tor infrastructure, uptime checkers, and crawlers costs resources. Your donations keep this service alive.
                </p>
            </div>

            <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1rem;">
                {{-- Bitcoin
                            --}}

                                            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.75rem;">
                    <div style="display:flex;align-items:center;gap:.65rem;min-width:160px;">
                        <div style="width:2.2rem;height:2.2rem;border-radius:.4rem;background:rgba(247,147,26,.1);border:1px solid rgba(247,147,26,.2);display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;color:#f7931a;flex-shrink:0;">₿</div>
                        <div>
                            <h3 style="color:#fff;font-weight:700;font-size:.82rem;margin:0;">Bitcoin (BTC)</h3>
                        
                           <p style="color:var(--color-gh-dim);font-size:.65rem;margin:0;">On-chain payment</p>
                        </div>
                    </div>
                    <div style="flex:1;min-width:200px;padding:.5rem .75rem;border-radius:.4rem;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);font-family:monospace;font-size:.72rem;color:var(--color-gh-accent);word-break:break-all;user-select:all;">
                        {{ config('Donate.btc') }}
                    </div>
                </div>

                {{-- ETH --}}
                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.75rem;">
                    <div style="display:flex;align-items:center;gap:.65rem;min-width:160px;">
                        <div style="width:2.2rem;height:2.2rem;border-radius:.4rem;background:rgba(88,166,255,.1);border:1px solid rgba(88,166,255,.2);display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;color:var(--color-gh-accent);flex-shrink:0;">Ξ</div>
                        <div>
                            <h3 style="color:#fff;font-weight:700;font-size:.82rem;margin:0;">Ethereum (ETH)</h3>
                            <p style="color:var(--color-gh-dim);font-size:.65rem;margin:0;">Decentralized payment</p>
                        </div>
                    </div>
                    <div style="flex:1;min-width:200px;padding:.5rem .75rem;border-radius:.4rem;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);font-family:monospace;font-size:.72rem;color:var(--color-gh-accent);word-break:break-all;user-select:all;">
                        {{ config('Donate.XMR') }}
                    </div>
                </div>
            </div>

            <div style="padding:.75rem 1.25rem;border-top:1px solid var(--color-gh-border);text-align:center;">
                <p style="font-size:.7rem;color:var(--color-gh-dim);font-style:italic;margin:0;">
                    All donations are anonymous. Thank you for keeping the darknet directory alive. ♥
                </p>
            </div>
        </section>
    </div>

</x-app.layouts>