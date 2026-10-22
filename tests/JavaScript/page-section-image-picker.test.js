import { test } from 'node:test';
import assert from 'node:assert/strict';
import picker from '../../resources/js/page-section-image-picker.js';

function component() {
    const state = picker();
    state.$el = { dataset: { endpoint: '/admin/pages/7/sections/images', selectedId: '', selectedUrl: '', selectedName: '' } };
    state.$refs = { dialog: { showModal() {}, close() {} } };
    state.$dispatch = () => {};
    state.init();
    // Alpine evaluates a click/keydown handler with $el bound to its child control.
    state.$el = { dataset: {} };
    return state;
}

test('child controls load, search and paginate on the current origin', async (t) => {
    for (const origin of ['http://127.0.0.1:8000', 'https://staging.example.test', 'https://www.example.test']) {
        t.mock.method(globalThis, 'fetch', async (url, options) => {
            assert.equal(url.origin, origin);
            assert.equal(url.pathname, '/admin/pages/7/sections/images');
            assert.equal(options.credentials, 'same-origin');
            assert.equal(options.headers.Accept, 'application/json');
            return { ok: true, redirected: false, json: async () => ({ data: [{ id: 1, name: 'Tiger_pic', url: '/storage/media/tiger.jpg' }], current_page: Number(url.searchParams.get('page')), last_page: 2 }) };
        });
        globalThis.window = { location: { origin } };
        const state = component();
        await state.open();
        assert.equal(state.error, '');
        assert.equal(state.images[0].url, '/storage/media/tiger.jpg');
        state.search = 'Tiger_pic & trees';
        await state.load(2);
        const request = fetch.mock.calls.at(-1).arguments[0];
        assert.equal(request.searchParams.get('search'), state.search);
        assert.equal(state.page, 2);
        assert.equal(state.lastPage, 2);
        let selectionEvent;
        state.$dispatch = (name) => { selectionEvent = name; };
        state.previewFailed = true;
        state.select(state.images[0]);
        assert.equal(state.selectedId, 1);
        assert.equal(state.previewFailed, false);
        assert.equal(selectionEvent, 'media-selection-changed');
        t.mock.restoreAll();
    }
    delete globalThis.window;
});

test('empty JSON is successful; failed and invalid responses can be retried', async (t) => {
    globalThis.window = { location: { origin: 'http://127.0.0.1:8000' } };
    const empty = { ok: true, redirected: false, json: async () => ({ data: [], current_page: 1, last_page: 1 }) };
    let response = empty;
    t.mock.method(globalThis, 'fetch', async () => response);
    const state = component();
    await state.load(1);
    assert.equal(state.error, '');
    assert.deepEqual(state.images, []);
    for (const failure of [
        { ok: false, status: 401 }, { ok: false, status: 403 }, { ok: false, status: 500 },
        { ok: true, redirected: true },
        { ok: true, json: async () => { throw new SyntaxError('HTML response'); } },
    ]) {
        response = failure;
        await state.load(1);
        assert.notEqual(state.error, '');
        assert.equal(state.loading, false);
        response = empty;
        await state.load(1);
        assert.equal(state.error, '');
    }
    delete globalThis.window;
});
