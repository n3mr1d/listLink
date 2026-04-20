/**
 * AdMate - Premium Ad Banner System for Tor
 * Optimized for performance, security, and high CTR.
 */
(function(window, document) {
    'use strict';

    const AdMate = {
        config: {
            apiBase: 'http://admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd.onion',
            badgeText: 'AdMate',
            interceptClass: 'search-result-link',
            rotationInterval: 30000, // 30s
            storageKey: '_admate_impressions'
        },

        init() {
            this.setupClickInterceptor();
            console.log('AdMate Initialized');
        },

        /**
         * Fetch and render banners into discovery containers
         * @param {string} url - The API endpoint URL
         * @param {boolean} autoRotate - Whether to enable auto-rotation
         */
        async getBanners(url, autoRotate = true) {
            this.lastUrl = url;
            await this._fetch(url);

            if (autoRotate && !this.rotationTimer) {
                this.rotationTimer = setInterval(() => {
                    this._fetch(this.lastUrl);
                }, this.config.rotationInterval);
            }
        },

        async _fetch(url) {
            try {
                // Handle onion domain normalization
                if (url.includes('admate3wrcqo2qeuok36b4wncwv7k6deei6riq2w62s36htgyahsaaqd')) {
                    url = url.replace('admate3wrcqo2qeuok36b4wncwv7k6deei6riq2w62s36htgyahsaaqd', 'admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd');
                }

                // Add cache buster for rotation
                const fetchUrl = url + (url.includes('?') ? '&' : '?') + 't=' + Date.now();

                const response = await fetch(fetchUrl, { 
                    cache: 'no-store', 
                    credentials: 'same-origin' 
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                
                const data = await response.json();
                if (!Array.isArray(data)) throw new Error('Invalid banner data format');

                const type = this.parseParam(url, 'type', '468-60');
                const count = Math.min(10, Math.max(1, parseInt(this.parseParam(url, 'count', '10'))));
                const [width, height] = type.split('-').map(Number);
                const idPrefix = `banner-place-${type.split('-')[0]}-`;

                // Render found banners
                for (let i = 1; i <= count; i++) {
                    const banner = data[i];
                    if (!banner || !banner.src) continue;

                    const container = document.getElementById(`${idPrefix}${i}`);
                    if (!container) continue;

                    this.render(container, {
                        src: this.sanitizeUrl(banner.src),
                        href: this.sanitizeUrl(banner.href || '#'),
                        alt: banner.alt || 'Advertisement',
                        width,
                        height,
                        type
                    });
                    
                    this.trackImpression(banner.id || banner.src);
                }
            } catch (err) {
                console.warn('AdMate: Failed to load banners', err);
            }
        },

        render(container, banner) {
            container.innerHTML = `
                <div class="admate-banner admate-type-${banner.type}" style="width: ${banner.width}px; height: ${banner.height}px;">
                    <a href="${banner.href}" target="_blank" rel="noopener noreferrer" class="admate-link">
                        <img src="${banner.src}" 
                             alt="${this.escapeHtml(banner.alt)}" 
                             width="${banner.width}" 
                             height="${banner.height}" 
                             loading="lazy" 
                             decoding="async">
                    </a>
                    <a href="${this.config.apiBase}" target="_blank" class="admate-badge">${this.config.badgeText}</a>
                </div>
            `;
        },

        /**
         * Intercepts clicks on search results to open an ad in the background/new tab
         */
        setupClickInterceptor() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest(`a.${this.config.interceptClass}`);
                if (!link) return;

                // Stop propagation and prevent default to control flow
                e.preventDefault();
                const originalUrl = link.href;

                // Reset logic per click is handled by the fact we always try once here
                // Requirement: "Open an ad link in a new tab/window, Then allow navigation to the actual result"
                
                // Open AdMate or a rotating ad link
                const adWindow = window.open(this.config.apiBase, '_blank');
                
                // If it's a search result, we usually want to navigate in the current tab
                // Wait a tiny bit to ensure the window.open call is registered
                setTimeout(() => {
                    window.location.href = originalUrl;
                }, 50);
            }, true);
        },

        parseParam(url, key, fallback) {
            const regex = new RegExp(`/${key}/([0-9\\-]+)`);
            const match = url.match(regex);
            return match ? match[1] : fallback;
        },

        sanitizeUrl(url) {
            if (!url) return '#';
            // Simple validation: must be http or https or protocol relative
            if (!/^(https?:\/\/|\/\/)/i.test(url)) return '#';
            return url;
        },

        escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        },

        trackImpression(id) {
            // Local tracking to prevent duplicates if needed
            let impressions = JSON.parse(localStorage.getItem(this.config.storageKey) || '[]');
            if (!impressions.includes(id)) {
                impressions.push(id);
                // Keep only last 100
                if (impressions.length > 100) impressions.shift();
                localStorage.setItem(this.config.storageKey, JSON.stringify(impressions));
            }
        }
    };

    // Expose globally
    window.AdMate = AdMate;
    
    // Auto-init
    AdMate.init();

})(window, document);
