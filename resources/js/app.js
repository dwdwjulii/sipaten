import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('[x-data]').forEach(el => {
        if (el.__x) {
            el.__x.$data.open = false;
        }
    });
});


Alpine.start();
