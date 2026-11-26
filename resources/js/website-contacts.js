export default function websiteContacts(initial = {}) {
    return {
        emails: [], phones: [], addresses: [], removedAddresses: [], sequence: 0,
        max: initial.max ?? 50, addressMax: initial.addressMax ?? 50,
        locationMax: initial.locationMax ?? 10, fieldLimit: initial.fieldLimit ?? 900,
        errors: initial.errors ?? {}, dirty: false, submitting: false,
        notice: '', pendingRemoval: null, unloadHandler: null,
        init() {
            this.emails = this.clean(initial.emails, 'emails');
            this.phones = this.clean(initial.phones, 'phones');
            this.addresses = (Array.isArray(initial.addresses) ? initial.addresses : []).map((row, index) => this.cleanAddress(row, index));
            this.removedAddresses = Array.isArray(initial.removedAddresses) ? initial.removedAddresses : [];
            this.unloadHandler = (event) => {
                if (this.dirty && !this.submitting) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            };
            if (typeof window !== 'undefined') window.addEventListener('beforeunload', this.unloadHandler);
        },
        destroy() {
            if (typeof window !== 'undefined') window.removeEventListener('beforeunload', this.unloadHandler);
        },
        clean(rows, group) {
            return (Array.isArray(rows) ? rows : []).map((row) => {
                row = row && typeof row === 'object' ? row : {};
                return {
                    _key: 'contact-' + (++this.sequence), id: row.id ?? '',
                    label: typeof row.label === 'string' ? row.label : '',
                    value: typeof row.value === 'string' ? row.value : '',
                    subtype: group === 'phones' ? (row.subtype ?? 'other') : null,
                    is_primary: [true, 1, '1'].includes(row.is_primary),
                    is_active: [true, 1, '1'].includes(row.is_active),
                };
            });
        },
        cleanAddress(row = {}, index = 0) {
            row = row && typeof row === 'object' ? row : {};
            const address = {
                _key: 'address-' + (++this.sequence), id: row.id ?? '',
                is_primary: [true, 1, '1'].includes(row.is_primary),
                is_active: [true, 1, '1'].includes(row.is_active),
                emails: this.clean(row.emails, 'emails'), phones: this.clean(row.phones, 'phones'),
                expanded: !row.id || Object.keys(this.errors).some((key) => key.startsWith('addresses.' + index + '.')),
            };
            for (const field of ['label', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country']) {
                address[field] = typeof row[field] === 'string' ? row[field] : '';
            }
            address._originalContactIds = initial.addressContactIds?.[address.id] ?? [...address.emails, ...address.phones].filter((c) => c.id).map((c) => c.id);
            return address;
        },
        list(group, address = null) { return address ? address[group] : this[group]; },
        contactCount() { return this.emails.length + this.phones.length + this.addresses.reduce((sum, a) => sum + a.emails.length + a.phones.length, 0); },
        estimatedFields() {
            return 40 + this.emails.length * 5 + this.phones.length * 6
                + this.addresses.reduce((sum, a) => sum + 16 + a.emails.length * 5 + a.phones.length * 6, 0)
                + this.removedAddresses.reduce((sum, a) => sum + 2 + a.contact_ids.length, 0);
        },
        room(extra) {
            if (this.estimatedFields() + extra > this.fieldLimit) {
                this.notice = 'The safe submission field budget is reached. Remove unused entries before adding more.';
                return false;
            }
            return true;
        },
        add(group, address = null) {
            const rows = this.list(group, address);
            const max = address ? this.locationMax : this.max;
            if (rows.length >= max || this.contactCount() >= 100) {
                this.notice = 'A maximum of ' + max + ' entries per contact type and 100 contacts across all scopes is allowed.';
                return;
            }
            if (!this.room(group === 'phones' ? 6 : 5)) return;
            rows.push({ _key: 'contact-' + (++this.sequence), id: '', label: '', value: '', subtype: group === 'phones' ? 'other' : null, is_primary: false, is_active: true });
            this.dirty = true;
        },
        addAddress() {
            if (this.addresses.length >= this.addressMax) {
                this.notice = 'A maximum of ' + this.addressMax + ' business addresses is allowed.';
                return;
            }
            if (!this.room(16)) return;
            this.addresses.push(this.cleanAddress({ is_active: 1 }, this.addresses.length));
            this.dirty = true;
        },
        path(group, address = null) {
            return address ? 'addresses.' + this.addresses.indexOf(address) + '.' + group + '.' : group + '.';
        },
        remapErrors(prefix, before, after) {
            const errors = {};
            for (const [path, messages] of Object.entries(this.errors)) {
                if (!path.startsWith(prefix)) { errors[path] = messages; continue; }
                const match = path.slice(prefix.length).match(/^(\d+)(\..*)$/);
                if (!match) { errors[path] = messages; continue; }
                const row = before[Number(match[1])];
                const index = after.findIndex((entry) => entry._key === row?._key);
                if (index >= 0) errors[prefix + index + match[2]] = messages;
            }
            this.errors = errors;
        },
        move(group, key, direction, address = null) {
            const rows = this.list(group, address);
            const index = rows.findIndex((row) => row._key === key);
            const next = index + direction;
            if (index < 0 || next < 0 || next >= rows.length) return;
            const before = [...rows];
            [rows[index], rows[next]] = [rows[next], rows[index]];
            this.remapErrors(this.path(group, address), before, rows);
            this.dirty = true;
        },
        primary(group, key, address = null) {
            const rows = this.list(group, address);
            if (key !== null && !rows.some((row) => row._key === key && row.is_active)) return;
            rows.forEach((row) => { row.is_primary = row._key === key; });
            this.dirty = true;
        },
        activeChanged(row) {
            if (!row.is_active && row.is_primary) {
                row.is_primary = false;
                this.notice = 'Primary designation cleared. Choose another active primary, or leave this collection without a primary.';
            }
            this.dirty = true;
        },
        requestRemove(group, key, address = null) {
            const row = this.list(group, address).find((entry) => entry._key === key);
            if (group === 'addresses' && (row?.emails.length || row?.phones.length)) {
                this.notice = 'Explicitly remove the address email and phone entries before removing their address.';
                row.expanded = true;
                return;
            }
            this.pendingRemoval = { group, key, addressKey: address?._key ?? null };
            this.$refs.contactRemove.showModal();
        },
        removeConfirmed() {
            if (!this.pendingRemoval) return;
            const { group, key, addressKey } = this.pendingRemoval;
            const address = addressKey ? this.addresses.find((entry) => entry._key === addressKey) : null;
            if (addressKey && !address) return;
            const rows = this.list(group, address);
            const index = rows.findIndex((entry) => entry._key === key);
            if (index < 0) return;
            const row = rows[index];
            if (group === 'addresses' && (row.emails.length || row.phones.length)) return;
            const before = [...rows];
            if (group === 'addresses' && row.id) this.removedAddresses.push({ id: row.id, contact_ids: [...row._originalContactIds] });
            rows.splice(index, 1);
            this.remapErrors(this.path(group, address), before, rows);
            this.notice = row.is_primary ? 'Primary removed. Choose another active primary, or leave this collection without a primary. Save Settings to apply.' : 'Entry removed from the form. Save Settings to apply the deletion.';
            this.pendingRemoval = null;
            this.dirty = true;
            this.$refs.contactRemove.close();
        },
        submit(event) {
            if (!this.room(0)) { event.preventDefault(); return; }
            this.submitting = true;
        },
        error(path) { return this.errors[path]?.[0] ?? ''; },
    };
}

