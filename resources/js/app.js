import './bootstrap';

import Alpine from 'alpinejs';
import mediaDisplayName from './media-display-name';
import pageSlug from './page-slug';

window.Alpine = Alpine;
Alpine.data('mediaDisplayName', mediaDisplayName);
Alpine.data('pageSlug', pageSlug);

Alpine.start();
