export default () => ({
    endpoint: '', selectedId: '', selectedUrl: '', selectedName: '', previewFailed: false, search: '', images: [], page: 1, lastPage: 1, loading: false, error: '',
    init() {
        // Capture the wrapper's route before $el refers to a clicked child control.
        this.endpoint = this.$el.dataset.endpoint;
        this.selectedId = this.$el.dataset.selectedId;
        this.selectedUrl = this.$el.dataset.selectedUrl;
        this.selectedName = this.$el.dataset.selectedName;
    },
    async open() {
        this.$refs.dialog.showModal();
        await this.load(1);
    },
    async load(page) {
        if (this.loading) return;
        this.loading = true;
        this.error = '';
        try {
            const url = new URL(this.endpoint, window.location.origin);
            url.searchParams.set('search', this.search);
            url.searchParams.set('page', page);
            const response = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (!response.ok || response.redirected) throw new Error('Unavailable');
            const result = await response.json();
            this.images = result.data;
            this.page = result.current_page;
            this.lastPage = result.last_page;
        } catch {
            this.images = [];
            this.error = 'Images could not be loaded. Please try again, or refresh and sign in if needed.';
        } finally {
            this.loading = false;
        }
    },
    select(image) {
        this.previewFailed = false;
        this.selectedId = image.id;
        this.selectedUrl = image.url;
        this.selectedName = image.name;
        this.$refs.dialog.close();
        this.$dispatch('media-selection-changed');
    },
    clear() {
        this.selectedId = '';
        this.selectedUrl = '';
        this.selectedName = '';
        const description = this.$el.closest('form').querySelector('[name="image_alt"]');
        if (description) description.value = '';
        this.$dispatch('media-selection-changed');
    },
});
