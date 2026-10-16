export default () => ({
    title: '',
    slug: '',
    manuallyEdited: false,

    init() {
        this.title = this.$el.dataset.title;
        this.slug = this.$el.dataset.slug;
        this.manuallyEdited = this.slug !== '';
    },

    suggest() {
        if (this.manuallyEdited) return;
        this.slug = this.title.normalize('NFKD').replace(/[\u0300-\u036f]/g, '')
            .toLowerCase().replace(/[_\s]+/g, '-').replace(/[^a-z0-9-]/g, '')
            .replace(/-+/g, '-').replace(/^-|-$/g, '');
    },
});
