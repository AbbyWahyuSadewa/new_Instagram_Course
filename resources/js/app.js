import './bootstrap';

import { livewire_hot_reload } from 'virtual:livewire-hot-reload'
livewire_hot_reload();

import Alpine from 'alpinejs';

window.Alpine = Alpine;

import intersect from '@alpinejs/intersect'

Alpine.plugin(intersect)
Alpine.start();
