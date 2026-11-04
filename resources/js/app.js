import '@tabler/core/dist/css/tabler.min.css';
import '@tabler/core/dist/js/tabler.min.js';

import Alpine from 'alpinejs';

import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css'; // O el CSS base de tom-select

// Hacerlo disponible globalmente para los scripts inline o de Alpine

window.Alpine = Alpine;
window.TomSelect = TomSelect;

Alpine.start();

