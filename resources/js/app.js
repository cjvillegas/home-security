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
import Print from 'vue-print-nb'

// import router file
import router from './router'

// vuex store
import {mapActions, mapGetters} from "vuex";
import store from './store/store'

// make element UI globally accessible
Vue.use(ElementUI, {  locale, size: 'small' })

//Vue Printer
Vue.use(Print);


import API from './api/index.js'
import EventBus from './services/EventBus'
import StringGenericService from './services/StringGenericService'
import DateGenericService from './services/DateGenericService'

Vue.prototype.$API = API
Vue.prototype.$EventBus = new EventBus()
Vue.prototype.$StringService = new StringGenericService()
Vue.prototype.$DateService = new DateGenericService()

import filters from './filters'

// vue filter
import numeral from 'numeral';
import numFormat from 'vue-filter-number-format'
import axios from 'axios';

// use the filter
Vue.filter('numFormat', numFormat(numeral));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const app = new Vue({
    el: '#app',
    router,
    store,
    data: {},
    computed: {
        ...mapGetters(['user'])
    },
    created() {
        this.getAuthUser()
    },
    methods: {
        getAuthUser() {
            this.$API.User.getAuthUser()
                .then(res => {
                    this.setUser(res.data)
                })
                .catch(err => {
                    console.error('Error: Global Get Auth User Error', err)
                })
        },

        getUsers() {
            this.$API.User.getCleanUsers()
            .then(res => {
                this.setUsers(res.data)
            })
            .catch(err => {
                console.error(`Error: Global User Fetching Error`)
            })
        },

        getTeams() {
            axios.get(`/admin/teams/all-teams`)
            .then(res => {
                this.setTeams(res.data)
            })
            .catch(err => {
                console.error(`Error: Global Teams Fetching Error`)
            })
        },

        getCurrentUser() {
            this.$API.User.getAuthUser()
            .then(res => {
                this.setProcesses(res.data)
            })
            .catch(err => {
                console.error(`Error: Global Process Fetching Error`)
            })
        },

        ...mapActions(['setUsers', 'setProcesses', 'setTeams', 'setUser'])
    }
});

// Add a response interceptor
window.axios.interceptors.response.use((response) => {
    return response;
}, (error) => {
    if (error.response) {
        if (error.response.status === 419) {
            app.$alert('Ops! Your session may have been expired. Please login.', 'UNAUTHENTICATED', {
                confirmButtonText: 'OK',
                callback: action => {
                    location.reload()
                }
            });
        }
        else if (error.response.status === 401) {
            app.$alert('Ops! You don\'t have the right permission to do this action. If you think that this is a problem please contact your administrator.', 'UNAUTHORIZED', {
                confirmButtonText: 'OK',
                callback: action => {
                }
            });
        }
        else if (error.response.status === 403) {
            app.$alert('Ops! You do not have enough permission or your session may have expired. Refresh the page to know if you need to login again.', 'FORBIDDEN', {
                confirmButtonText: 'OK',
                callback: action => {
                    location.href = '/admin'
                }
            });
        }
        else if (error.response.status >= 500) {
            app.$alert('Ops! Something went wrong. Please report this to your technical support.', 'SERVER ERROR', {
                confirmButtonText: 'OK',
                callback: action => {}
            });
        }
    }
    return Promise.reject(error);
});
