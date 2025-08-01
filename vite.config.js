import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import livewire from '@laravel/livewire-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        livewire(), // 👈 this must be here!
    ],
});
