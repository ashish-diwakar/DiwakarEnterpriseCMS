import './bootstrap';

import Alpine from 'alpinejs';
import mediaDisplayName from './media-display-name';

window.Alpine = Alpine;
Alpine.data('mediaDisplayName', mediaDisplayName);

Alpine.start();
