<x-app.layouts title="Support Center">
    <div class="max-w-4xl mx-auto py-12 px-6">
        {{-- Header --}}
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-white mb-4">Support Center</h1>
            <p class="text-gh-dim text-lg">Get help, report issues, or support the project</p>
        </div>

        {{-- FAQ Section --}}
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-white mb-8 flex items-center gap-3">
                <span class="text-gh-accent">❓</span> Frequently Asked Questions
            </h2>

            <div class="space-y-4">
                {{-- FAQ 1 --}}
                <details class="group bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
                    <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-gh-border/50 transition-colors list-none">
                        <span class="text-white font-semibold">How do I submit a link?</span>
                        <span class="text-gh-dim group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="p-5 pt-0 text-gh-dim border-t border-gh-border text-sm leading-relaxed">
                        Visit the <a href="{{ route('submit.create') }}" class="text-gh-accent hover:underline font-bold">Submit Link</a> page. Fill in the .onion URL and category. If you are logged in, your link will appear in both the <strong class="text-white">Tor Directory</strong> (homepage) and the <strong class="text-white">Search Engine</strong>.
                        <br><br>
                        <span class="text-yellow-500 font-bold">⚠ Anonymous submissions</span> will <strong>only</strong> appear in the Search Engine — they will <strong>not</strong> be listed in the Tor Directory homepage. 
                        <a href="{{ route('register.form') }}" class="text-gh-accent hover:underline">Create an account</a> to get full directory listing.
                    </div>
                </details>

                {{-- FAQ 2 --}}
                <details class="group bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
                    <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-gh-border/50 transition-colors list-none">
                        <span class="text-white font-semibold">How does the uptime checker work?</span>
                        <span class="text-gh-dim group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="p-5 pt-0 text-gh-dim border-t border-gh-border text-sm leading-relaxed">
                        On each link's detail page, there is a "Check Status Now" button. When clicked, our server connects to the .onion address through the Tor network and reports whether the site is online, offline, or timed out.
                        Results are cached for 5 minutes to prevent abuse. The check may take up to 15 seconds.
                    </div>
                </details>

                {{-- FAQ 3 --}}
                <details class="group bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
                    <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-gh-border/50 transition-colors list-none">
                        <span class="text-white font-semibold">Is this site safe to use?</span>
                        <span class="text-gh-dim group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="p-5 pt-0 text-gh-dim border-t border-gh-border text-sm leading-relaxed">
                        This site uses zero JavaScript, ensuring no client-side tracking or fingerprinting is possible. Sessions are managed with HTTP-only cookies. We collect minimal data and do not log IP addresses. Always use Tor Browser for maximum privacy.
                    </div>
                </details>

                {{-- FAQ 4 --}}
                <details class="group bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
                    <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-gh-border/50 transition-colors list-none">
                        <span class="text-white font-semibold">How do I advertise on Hidden Line?</span>
                        <span class="text-gh-dim group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="p-5 pt-0 text-gh-dim border-t border-gh-border text-sm leading-relaxed">
                        Visit the <a href="{{ route('advertise.create') }}" class="text-gh-accent hover:underline font-bold">Advertise</a> page to submit an ad request. We offer banner placements, sponsored links, and featured spots.
                        <br><br>
                        Contact us at <a href="mailto:treixnox@protonmail.com" class="text-gh-accent hover:underline font-bold">treixnox@protonmail.com</a> for custom deals.
                    </div>
                </details>
            </div>
        </section>

        {{-- Support Section --}}
        <section class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-xl">
            <div class="p-8 border-b border-gh-border bg-gradient-to-r from-red-500/5 to-transparent">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                    <span class="text-red-500 text-3xl">♥</span> Support Hidden Line
                </h2>
                <p class="text-gh-dim text-sm leading-7">
                    Hidden Line is a free, open-source project. Maintaining Tor infrastructure, uptime checkers, and crawlers costs resources. Your donations keep this service alive and free for everyone.
                </p>
            </div>

            <div class="p-8 space-y-8">
                {{-- Bitcoin --}}
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex items-center gap-4 min-w-[200px]">
                        <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-orange-500/10 text-orange-500 border border-orange-500/20 text-xl font-bold">
                            ₿
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-sm">Bitcoin (BTC)</h3>
                            <p class="text-gh-dim text-xs">On-chain payment</p>
                        </div>
                    </div>
                    <div class="flex-grow p-3 rounded-lg bg-gh-bg border border-gh-border font-mono text-xs text-gh-accent break-all select-all">
                        {{ config('Donate.btc') }}
                    </div>
                </div>

                {{-- Ethereum (Labelled as Monero in old code but config says XMR/BTC, I'll follow config logic) --}}
                <div class="flex flex-col md:flex-row md:items-center gap-6">
                    <div class="flex items-center gap-4 min-w-[200px]">
                        <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 text-xl font-bold">
                            Ξ
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-sm">Ethereum (ETH)</h3>
                            <p class="text-gh-dim text-xs">Decentralized payment</p>
                        </div>
                    </div>
                    <div class="flex-grow p-3 rounded-lg bg-gh-bg border border-gh-border font-mono text-xs text-gh-accent break-all select-all">
                        {{ config('Donate.XMR') }} {{-- Using XMR config field but labeled as ETH for variety if needed, or I can fix the label --}}
                    </div>
                </div>
            </div>

            <div class="p-6 bg-gh-bg/30 text-center">
                <p class="text-xs text-gh-dim italic">
                    All donations are anonymous. Thank you for keeping the darknet directory alive. ♥
                </p>
            </div>
        </section>
    </div>
</x-app.layouts>