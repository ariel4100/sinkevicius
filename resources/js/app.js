require('./bootstrap');

require('moment');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'jodit/build/jodit.min.css'
import JoditVue from 'jodit-vue'
import Slick from 'vue-slick';
import Multiselect from 'vue-multiselect'
import store from './Store'

Vue.mixin({
    data(){
        return {
            menu: [
                {
                    nombre: 'Inicio',
                    route: 'home',
                    url: 'home',
                    mostrar: 1,
                },
                {
                    nombre: 'EMPRESA',
                    route: 'empresa',
                    url: 'empresa',
                    mostrar: 1,
                },
                {
                    nombre: 'Productos',
                    route: 'familias',
                    url: 'familias,subfamilias,productos,producto',
                    mostrar: 1,
                },
                {
                    nombre: 'CALIDAD',
                    route: 'calidad',
                    url: 'calidad',
                    mostrar: 1,
                },
                {
                    nombre: 'SOLICITAR PRESUPUESTO',
                    route: 'presupuesto',
                    url: 'presupuesto',
                    mostrar: 1,
                },
                {
                    nombre: 'CONTACTO',
                    route: 'contacto',
                    url: 'contacto',
                    mostrar: 1,
                },
            ]
        }
    },
    methods: {
        t(key) {
            // console.log('aca')
            let item = 0
            if(this.$page.traducciones){
                item = this.$page.traducciones.find((item) => {
                    if(item.key == key){
                        return item
                    }
                })
            }
            if(item){
                return item.traduccion
            }else{
                return key
            }
        },
        logout() {
            axios.post(route('logout').url()).then(response => {
                window.location = route('home');
            })
        },
    }
})
Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(JoditVue)

Vue.component(JoditVue)
Vue.component(Slick)
Vue.component('multiselect', Multiselect)

const app = document.getElementById('app');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
    // store,
}).$mount(app);
