import './bootstrap';

import Alpine from 'alpinejs';
import mediaDisplayName from './media-display-name';
import pageSlug from './page-slug';
import pageSectionImagePicker from './page-section-image-picker';

window.Alpine = Alpine;
Alpine.data('mediaDisplayName', mediaDisplayName);
Alpine.data('pageSlug', pageSlug);
Alpine.data('pageSectionImagePicker', pageSectionImagePicker);

Alpine.start();
