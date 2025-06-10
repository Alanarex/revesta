import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import fg from 'fast-glob';

const scssEntries = fg.sync('resources/scss/**/*.scss');
const jsEntries = fg.sync('resources/js/**/*.js');

export default defineConfig({
    plugins: [
        laravel({
            input: [
                ...scssEntries,
                ...jsEntries,
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
        },
    },
});
