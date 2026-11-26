import { test } from 'node:test';
import assert from 'node:assert/strict';
import websiteContacts from '../../resources/js/website-contacts.js';

function editor(initial = {}) {
    const state = websiteContacts(initial);
    state.$refs = { contactRemove: { showModal() {}, close() {} } };
    state.init();
    return state;
}

test('adds separate email/phone rows and enforces the UI limit without reloading', () => {
    const state = editor({ max: 4 });
    for (let i = 0; i < 5; i++) state.add('emails');
    state.add('phones');
    assert.equal(state.emails.length, 4);
    assert.equal(state.phones.length, 1);
    assert.equal(state.phones[0].subtype, 'other');
    assert.match(state.notice, /maximum of 4/);
    assert.equal(new Set([...state.emails, ...state.phones].map((row) => row._key)).size, 5);
});

test('selecting a new primary clears the old flag and deactivation chooses no replacement', () => {
    const state = editor({ emails: [{ value: 'a@example.test', is_active: 1, is_primary: 1 }, { value: 'b@example.test', is_active: 1 }] });
    state.primary('emails', state.emails[1]._key);
    assert.deepEqual(state.emails.map((row) => row.is_primary), [false, true]);
    state.emails[1].is_active = false;
    state.activeChanged(state.emails[1]);
    assert.deepEqual(state.emails.map((row) => row.is_primary), [false, false]);
    state.primary('emails', state.emails[1]._key);
    assert.equal(state.emails[1].is_primary, false);
    assert.match(state.notice, /Choose another active primary/);
});

test('removal waits for confirmation and affects only the selected row', () => {
    const state = editor({ emails: [{ id: 7, is_active: 1, is_primary: 1 }, { id: 8, is_active: 1 }] });
    state.requestRemove('emails', state.emails[0]._key);
    assert.deepEqual(state.emails.map((row) => row.id), [7, 8]);
    state.removeConfirmed();
    assert.deepEqual(state.emails.map((row) => row.id), [8]);
    assert.equal(state.emails[0].is_primary, false);
    assert.match(state.notice, /Primary removed/);
    assert.equal(state.dirty, true);
});

test('ordering preserves identities and moves validation messages with their row', () => {
    const state = editor({ emails: [{ id: 1 }, { id: 2 }], errors: { 'emails.0.value': ['Invalid'] } });
    state.move('emails', state.emails[1]._key, -1);
    assert.deepEqual(state.emails.map((row) => row.id), [2, 1]);
    assert.deepEqual(state.errors, { 'emails.1.value': ['Invalid'] });
    state.move('emails', state.emails[0]._key, -1);
    assert.deepEqual(state.emails.map((row) => row.id), [2, 1]);
});

test('unsaved edits trigger navigation protection but a deliberate save does not', () => {
    const state = editor();
    let prevented = 0;
    const event = { preventDefault() { prevented++; } };
    state.unloadHandler(event);
    assert.equal(prevented, 0);
    state.add('phones');
    state.unloadHandler(event);
    assert.equal(prevented, 1);
    state.submitting = true;
    state.unloadHandler(event);
    assert.equal(prevented, 1);
});

test('new addresses have independent contacts and primary choices without moving defaults', () => {
    const state = editor({ emails: [{ id: 1, is_primary: 1, is_active: 1 }] });
    state.addAddress();
    state.addAddress();
    const [first, second] = state.addresses;
    state.add('emails', first);
    state.add('emails', second);
    state.primary('emails', first.emails[0]._key, first);
    assert.equal(state.emails[0].is_primary, true);
    assert.equal(first.emails[0].is_primary, true);
    assert.equal(second.emails[0].is_primary, false);
    state.primary('addresses', first._key);
    state.primary('addresses', second._key);
    assert.equal(first.is_primary, false);
    assert.equal(first.emails[0].is_primary, true);
    assert.equal(second.is_primary, true);
});

test('address removal requires explicit child removal and preserves exact original ID acknowledgment', () => {
    const state = editor({ addresses: [{ id: 2, is_active: 1, emails: [{ id: 7, is_active: 1 }] }], addressContactIds: { 2: [7, 8] } });
    const address = state.addresses[0];
    state.requestRemove('addresses', address._key);
    assert.equal(state.pendingRemoval, null);
    assert.match(state.notice, /Explicitly remove/);
    state.requestRemove('emails', address.emails[0]._key, address);
    state.removeConfirmed();
    state.requestRemove('addresses', address._key);
    state.removeConfirmed();
    assert.deepEqual(state.removedAddresses, [{ id: 2, contact_ids: [7, 8] }]);
    assert.equal(state.addresses.length, 0);
});

test('nested errors expand affected addresses and remain attached while addresses and contacts move', () => {
    const state = editor({ addresses: [{ id: 1, emails: [{ id: 5 }, { id: 6 }] }, { id: 2 }], errors: { 'addresses.0.emails.0.value': ['Invalid email'] } });
    const address = state.addresses[0];
    assert.equal(address.expanded, true);
    assert.equal(state.addresses[1].expanded, false);
    state.move('emails', address.emails[0]._key, 1, address);
    assert.equal(state.error('addresses.0.emails.1.value'), 'Invalid email');
    state.move('addresses', address._key, 1);
    assert.equal(state.error('addresses.1.emails.1.value'), 'Invalid email');
    assert.equal(state.addresses[1].expanded, true);
});

test('nested type and aggregate limits and the field budget prevent oversized form construction', () => {
    const state = editor({ locationMax: 2, fieldLimit: 900 });
    state.addAddress();
    for (let i = 0; i < 3; i++) state.add('phones', state.addresses[0]);
    assert.equal(state.addresses[0].phones.length, 2);
    const limited = editor({ fieldLimit: 40 });
    limited.addAddress();
    limited.add('emails');
    assert.equal(limited.addresses.length, 0);
    assert.equal(limited.emails.length, 0);
    const aggregate = editor({ emails: Array.from({ length: 50 }, () => ({})), phones: Array.from({ length: 50 }, () => ({})) });
    aggregate.addAddress();
    aggregate.add('emails', aggregate.addresses[0]);
    assert.equal(aggregate.contactCount(), 100);
    assert.equal(aggregate.addresses[0].emails.length, 0);
});

test('deactivating a location keeps its children and their primary flags for later reactivation', () => {
    const state = editor({ addresses: [{ id: 1, is_active: 1, is_primary: 1, emails: [{ id: 7, is_active: 1, is_primary: 1 }] }] });
    const address = state.addresses[0];
    address.is_active = false;
    state.activeChanged(address);
    assert.equal(address.is_primary, false);
    assert.equal(address.emails[0].is_primary, true);
    assert.equal(address.emails[0].is_active, true);
    address.is_active = true;
    assert.equal(address.emails.length, 1);
});
