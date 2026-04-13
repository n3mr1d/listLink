<x-app.layouts title="About {{ config('app.name') }}" description="Learn more about {{ config('app.name') }}, the ultimate Tor-optimized .onion directory providing verified links, uptime monitoring, and decentralized access.">

    <style>
        .about-features { display: grid; grid-template-columns: 1fr; gap: .75rem; }
        @media (min-width: 640px) { .about-features { grid-template-columns: 1fr 1fr; } }
    </style>

    <div style="max-width:760px;margin:0 auto;padding:1.5rem 0 3rem;">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:2rem;">
            <h1 style="font-size:1.75rem;font-weight:900;color:#fff;margin:0 0 .5rem;letter-spacing:-.02em;">About {{ config('app.name') }}</h1>
            <p style="color:var(--color-gh-dim);font-size:.9rem;margin:0;">Your privacy-focused, uncensored gateway to the deep web.</p>
        </div>

        {{-- Mission --}}
        <section style="border:1px solid var(--color-gh-border);border-radius:.6rem;padding:1.25rem;margin-bottom:1rem;">
            <h2 style="color:var(--color-gh-accent);font-size:.95rem;font-weight:800;margin:0 0 .75rem;display:flex;align-items:center;gap:.5rem;">
                Our Mission
            </h2>
            <p style="color:var(--color-gh-text);line-height:1.7;font-size:.88rem;margin:0;">
                {{ config('app.name') }} is built with a single philosophy: <strong style="color:#fff;">freedom of information without compromise</strong>.
                We provide a robust, meticulously verified directory of Tor
                <code style="background:var(--color-gh-btn-bg);padding:.1rem .35rem;border-radius:.25rem;font-size:.8rem;border:1px solid var(--color-gh-border);">.onion</code>
                services. We don't track you, we don't censor content, and we provide raw, unfiltered access to the Tor network.
            </p>
        </section>

        {{-- Features Grid --}}
        <section style="margin-bottom:1.5rem;">
            <h2 style="color:#fff;font-size:.85rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;margin:0 0 .75rem;">
                Core Features
            </h2>
            <div class="about-features">
                @foreach([
                    [ 'color' => 'rgba(88,166,255,.1)', 'title' => 'Uptime Monitoring', 'desc' => 'We regularly check .onion links to ensure they are live. Our automated system checks uptime status to save you time.'],
                    [ 'color' => 'rgba(74,222,128,.1)', 'title' => 'Deep Web Crawler', 'desc' => config('app.name') . ' uses a custom-built, Tor-proxied crawler to actively discover and index new .onion links.'],
                    [ 'color' => 'rgba(247,147,26,.1)', 'title' => 'Crypto Economics', 'desc' => 'All premium ad placements are powered exclusively through Bitcoin gateway integrations for full financial privacy.'],
                    [ 'color' => 'rgba(168,85,247,.1)', 'title' => 'Tor-Optimized', 'desc' => 'Our interface is purposely lightweight. We avoid heavy JavaScript and bulky assets for maximum speed over Tor relays.'],
                ] as $feat)
                    <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;padding:1rem;display:flex;align-items:flex-start;gap:.75rem;">
                        <div>
                            <h3 style="color:#fff;font-weight:700;font-size:.85rem;margin:0 0 .3rem;">{{ $feat['title'] }}</h3>
                            <p style="color:var(--color-gh-dim);font-size:.75rem;line-height:1.6;margin:0;">{{ $feat['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Contact --}}
        <section style="border:1px solid var(--color-gh-border);border-radius:.6rem;padding:1.5rem;text-align:center;">
            <h2 style="color:#fff;font-size:1.1rem;font-weight:800;margin:0 0 .5rem;">Get In Touch</h2>
            <p style="color:var(--color-gh-dim);font-size:.82rem;max-width:480px;margin:0 auto .75rem;line-height:1.6;">
                We believe in community-driven curation. If you want to advertise, propose a feature, or provide feedback, reach out:
            </p>
            <a href="mailto:{{ config('site.contact_email') }}"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.5rem;background:var(--color-gh-accent);color:#0d1117;font-weight:800;border-radius:2rem;text-decoration:none;font-size:.82rem;">
                {{ config('site.contact_email') }}
            </a>
        </section>
    </div>

</x-app.layouts>
