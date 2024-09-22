import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // Si vous utilisez Vue.js

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/js/firebase_auth.js', // Inclure le fichier Firebase
                'resources/sass/app.scss',
            ],
            refresh: true,
        }),
        vue() // Si vous utilisez Vue.js
    ],
});
