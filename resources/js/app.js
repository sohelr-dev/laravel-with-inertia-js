import { createApp, h, render } from 'vue';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
        setup ({ el, App, props, plugin}){
            createApp({ render: ()=> h(App, props) })
            .use(plugin)
            .mount(el);
        },

    });
