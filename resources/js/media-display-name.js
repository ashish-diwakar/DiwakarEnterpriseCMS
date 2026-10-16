export default () => ({
    originalName: '',
    title: null,
    draft: '',
    saving: false,
    error: '',
    message: '',

    init() {
        this.originalName = this.$el.dataset.originalName;
        this.title = this.$el.dataset.title || null;
        this.draft = this.$el.dataset.draft;
    },

    get displayName() {
        return this.title ?? this.originalName;
    },

    cancel(button) {
        if (this.saving) return;
        this.draft = this.title ?? '';
        this.error = '';
        button.closest('details').open = false;
    },

    async save(form) {
        if (this.saving) return;
        this.saving = true;
        this.error = '';
        this.message = '';

        try {
            const response = await fetch(form.action, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.elements._token.value,
                },
                body: JSON.stringify({ title: this.draft }),
            });

            if (response.status === 422) {
                const data = await response.json();
                this.error = data.errors?.title?.[0] || 'Please check the display name.';
            } else if ([401, 403, 419].includes(response.status) || response.redirected) {
                this.error = 'Your session or permission may have changed. Refresh the page and sign in again if needed.';
            } else if (!response.ok) {
                this.error = 'The display name could not be saved. Please try again.';
            } else {
                const data = await response.json();
                if (data.title !== null && typeof data.title !== 'string') throw new Error('Invalid response');
                this.title = data.title;
                this.draft = data.title ?? '';
                this.message = 'Display name saved.';
                form.closest('details').open = false;
            }
        } catch {
            this.error = 'The display name could not be saved. Please try again.';
        } finally {
            this.saving = false;
        }
    },
});
