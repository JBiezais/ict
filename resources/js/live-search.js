export function liveSearch(config = {}) {
    const baseUrl = config.baseUrl ?? '/';
    const listSelector = config.listSelector ?? '[data-posts-list]';
    const minLength = config.minLength ?? 2;
    const debounceMs = config.debounceMs ?? 300;

    return {
        debounceTimer: null,
        abortController: null,

        handleInput() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.fetchResults(), debounceMs);
        },

        handleSubmit(event) {
            event.preventDefault();
            clearTimeout(this.debounceTimer);
            this.fetchResults();
        },

        async fetchResults() {
            const form = this.$refs?.searchForm ?? this.$el?.querySelector('form');
            const searchInput = form?.querySelector('input[name="search"]');
            const searchValue = (searchInput?.value ?? '').trim();

            if (searchValue.length > 0 && searchValue.length < minLength) {
                return;
            }

            if (!form || !(form instanceof HTMLFormElement)) {
                return;
            }

            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();

            const formData = new FormData(form);
            formData.set('search', searchValue);
            formData.set('page', '1');
            formData.set('_fragment', '1');

            const params = new URLSearchParams(formData);
            const url = `${baseUrl}?${params}`;

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.abortController.signal,
                });

                if (!response.ok) return;

                const html = await response.text();
                const container = document.querySelector(listSelector);
                if (container) {
                    container.innerHTML = html;
                }

                const urlForHistory = new URL(url, window.location.origin);
                urlForHistory.searchParams.delete('_fragment');
                history.replaceState(null, '', urlForHistory.pathname + urlForHistory.search);
            } catch (err) {
                if (err.name === 'AbortError') return;
                throw err;
            }
        },
    };
}
