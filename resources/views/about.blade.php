<x-app.layouts title="About Hidden Line" description="Learn more about Hidden Line, the ultimate Tor-optimized .onion directory providing verified links, uptime monitoring, and decentralized access.">
    <div class="max-w-4xl mx-auto py-12 px-6">
        {{-- Header --}}
        <div class="text-center mb-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gh-accent/10 mb-6 text-gh-accent">
                <span class="text-3xl font-bold">ℹ</span>
            </div>
            <h1 class="text-4xl font-extrabold text-white mb-4">About Hidden Line</h1>
            <p class="text-gh-dim text-lg">Your privacy-focused, uncensored gateway to the deep web.</p>
        </div>

        {{-- Mission Section --}}
        <section class="mb-16 bg-gh-bar-bg border border-gh-border rounded-2xl p-8 shadow-sm">
            <h2 class="text-gh-accent text-xl font-bold mb-4 flex items-center gap-3">
                <span class="text-xl">🛡</span> Our Mission
            </h2>
            <p class="text-gh-text leading-relaxed text-lg">
                Hidden Line is built with a single philosophy: <strong class="text-white">freedom of information without compromise</strong>. 
                We provide a robust, meticulously verified directory of Tor <code class="bg-gh-bg px-1.5 py-0.5 rounded border border-gh-border text-sm">.onion</code> services. 
                We don't track you, we don't censor content, and we provide raw, unfiltered access to the Tor network.
            </p>
        </section>

        {{-- Core Features Grid --}}
        <section class="mb-16">
            <h2 class="text-white text-xl font-bold mb-8 flex items-center gap-3">
                <i class="fas fa-bolt text-yellow-500"></i> Core Features
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Feature 1: Uptime Monitoring --}}
                <div class="p-6 bg-gh-bar-bg border border-gh-border rounded-xl transition-colors hover:border-gh-accent group">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-lg bg-blue-500/10 text-blue-500">
                            <span class="text-xl">🖥</span>
                        </div>
                        <div>
                            <h3 class="text-white font-bold mb-2">Uptime Monitoring</h3>
                            <p class="text-gh-dim text-sm leading-6">We regularly check <code class="text-xs">.onion</code> links to ensure they are live. Our automated system checks uptime status to save you time before you visit.</p>
                        </div>
                    </div>
                </div>

                {{-- Feature 2: Deep Web Crawler --}}
                <div class="p-6 bg-gh-bar-bg border border-gh-border rounded-xl transition-colors hover:border-gh-accent group">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-lg bg-green-500/10 text-green-500">
                            <span class="text-xl">🕷</span>
                        </div>
                        <div>
                            <h3 class="text-white font-bold mb-2">Deep Web Crawler</h3>
                            <p class="text-gh-dim text-sm leading-6">Hidden Line uses a custom-built, Tor-proxied crawler to actively discover and index new <code class="text-xs">.onion</code> links, keeping our directory comprehensive.</p>
                        </div>
                    </div>
                </div>

                {{-- Feature 3: Crypto Economics --}}
                <div class="p-6 bg-gh-bar-bg border border-gh-border rounded-xl transition-colors hover:border-gh-accent group">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-lg bg-orange-500/10 text-orange-500">
                            <span class="text-xl">₿</span>
                        </div>
                        <div>
                            <h3 class="text-white font-bold mb-2">Crypto Economics</h3>
                            <p class="text-gh-dim text-sm leading-6">All premium ad placements and services on Hidden Line are powered exclusively through automated Bitcoin gateway integrations for full financial privacy.</p>
                        </div>
                    </div>
                </div>

                {{-- Feature 4: Tor-Optimized --}}
                <div class="p-6 bg-gh-bar-bg border border-gh-border rounded-xl transition-colors hover:border-gh-accent group">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-lg bg-purple-500/10 text-purple-500">
                            <span class="text-xl">🚀</span>
                        </div>
                        <div>
                            <h3 class="text-white font-bold mb-2">Tor-Optimized</h3>
                            <p class="text-gh-dim text-sm leading-6">Our interface is purposely lightweight. We avoid heavy JavaScript and bulky assets, minimizing network ping and ensuring maximum speed when routing through multiple Tor relays.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact Section --}}
        <section class="bg-gradient-to-r from-gh-bar-bg to-gh-bg border border-gh-border rounded-2xl p-10 text-center shadow-xl">
            <h2 class="text-white text-2xl font-bold mb-4">Get In Touch</h2>
            <p class="text-gh-dim mb-8 max-w-xl mx-auto leading-relaxed text-sm">
                We believe in community-driven curation. If you want to advertise, propose a feature, or provide feedback, feel free to reach out to our secure email:
            </p>
            <a href="mailto:treixnox@protonmail.com" class="inline-flex items-center gap-3 px-8 py-3 bg-gh-accent text-gh-bg font-extrabold rounded-full hover:bg-blue-400">
                ✉ treixnox@protonmail.com
            </a>
        </section>
    </div>
</x-app.layouts>
