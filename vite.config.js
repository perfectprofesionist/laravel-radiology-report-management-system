import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        https: true, // Enable HTTPS
        host: true,
        port: 5173,
        hmr: {
            host: 'radiologist_cms.local'
        },
        cors: {
            origin: ['https://radiologist_cms.local']
        }
    },
});
