/**
 * TabTracker – Local-first interest tracking.
 * Saves user interactions (tag clicks, page views) to localStorage.
 * Sends top interests as query params when fetching the feed.
 */
window.TabTracker = (function () {
    const STORAGE_KEY = 'tw_interests';
    const MAX_TRACKED = 20;

    function getInterests() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        } catch {
            return {};
        }
    }

    function saveInterests(data) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    }

    return {
        /**
         * Track a tag click, incrementing its weight.
         */
        trackTag(tag) {
            const interests = getInterests();
            tag = tag.toLowerCase().replace('#', '');
            interests[tag] = (interests[tag] || 0) + 1;

            // Keep only top N tags by weight
            const sorted = Object.entries(interests)
                .sort((a, b) => b[1] - a[1])
                .slice(0, MAX_TRACKED);
            saveInterests(Object.fromEntries(sorted));
        },

        /**
         * Track a generic page view event (for analytics).
         */
        trackPageView() {
            const interests = getInterests();
            interests.__views = (interests.__views || 0) + 1;
            saveInterests(interests);
        },

        /**
         * Get top 5 interest tags as a comma-separated string.
         */
        getTopInterests(limit = 5) {
            const interests = getInterests();
            return Object.entries(interests)
                .filter(([k]) => !k.startsWith('__'))
                .sort((a, b) => b[1] - a[1])
                .slice(0, limit)
                .map(([k]) => k)
                .join(',');
        },

        /**
         * Build a URL with interests as query param.
         */
        buildFeedUrl(baseUrl = '/feed') {
            const interests = this.getTopInterests();
            if (!interests) return baseUrl;
            return baseUrl + '?interests=' + encodeURIComponent(interests);
        },

        /**
         * Clear all tracked interests.
         */
        clear() {
            localStorage.removeItem(STORAGE_KEY);
        },
    };
})();
