require('./bootstrap');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
const files = require.context('./components', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

import Vue from 'vue'

// Add Element Packages
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import 'element-ui/lib/theme-chalk/display.css'
import locale from 'element-ui/lib/locale/lang/en'

// make element UI globally accessible
Vue.use(ElementUI, {  locale, size: 'small' })

import API from './api/index.js'
import EventBus from './services/EventBus'
import StringGenericService from './services/StringGenericService'

Vue.prototype.$API = API
Vue.prototype.$EventBus = new EventBus()
Vue.prototype.$StringService = new StringGenericService()

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const app = new Vue({
    el: '#app',
    data: {},
    created() {

    },
});

// Add a response interceptor
window.axios.interceptors.response.use((response) => {
    return response;
}, (error) => {
    if (error.response) {
        if ([401, 419].includes(error.response.status)) {
            app.$alert('Opss! Your session may have been expired. Please login.', 'UNAUTHENTICATED', {
                confirmButtonText: 'OK',
                callback: action => {
                    location.reload()
                }
            });
        }
        else if (error.response.status === 403) {
            app.$alert('Opss! You do not have enough permission or your session may have expired. Refresh the page to know if you need to login again.', 'FORBIDDEN', {
                confirmButtonText: 'OK',
                callback: action => {
                    location.reload()
                }
            });
        }
        else if (error.response.status >= 500) {
            app.$alert('Opss! Something went wrong. Please report this to your technical support.', 'SERVER ERROR', {
                confirmButtonText: 'OK',
                callback: action => {}
            });
        }
    }
    return Promise.reject(error);
});
