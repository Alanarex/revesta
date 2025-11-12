import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import fg from 'fast-glob';

const scssEntries = fg.sync('resources/scss/**/*.scss');
const jsEntries = fg.sync('resources/js/**/*.js');
const imageEntries = fg.sync('resources/images/**/*.{svg,png,jpg,jpeg,gif,webp}');

export default defineConfig(({ mode }) => {
    // Load env file based on `mode` in the current working directory.
    const env = loadEnv(mode, process.cwd(), '');

    const appUrl = env.APP_URL || 'http://revesta.local';
    const hmrHost = new URL(appUrl).hostname;

    return {
        plugins: [
            laravel({
                input: [
                    ...scssEntries,
                    ...jsEntries,
                    ...imageEntries,
                ],
                refresh: true,
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources'),
            },
        },
        server: {
            host: '0.0.0.0', // Accept connections from any host
            port: 5173,
            strictPort: true,
            hmr: {
                host: hmrHost, // Use hostname from APP_URL in .env
            },
        },
    };
});
