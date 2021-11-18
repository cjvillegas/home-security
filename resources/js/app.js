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

// import router file
import router from './router'

// vuex store
import {mapActions, mapGetters} from "vuex";
import store from './store/store'

// vue filters
import filters from './filters'

import moment from 'moment'

// make element UI globally accessible
Vue.use(ElementUI, {  locale, size: 'small' })

import API from './api/index.js'
import EventBus from './services/EventBus'
import StringGenericService from './services/StringGenericService'
import DateGenericService from './services/DateGenericService'

Vue.prototype.$API = API
Vue.prototype.$EventBus = new EventBus()
Vue.prototype.$StringService = new StringGenericService()
Vue.prototype.$DateService = new DateGenericService()

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
        let pathname = window.location.pathname

        console.log(pathname)

        // if on public
        if (pathname.includes('/public/')) {
            return
        }

        /**
         * optionally load the data on specific pages. since we only use vuex in
         * selected pages, we will only load the data in those pages, not to all pages
         */
        if (pathname === '/admin/reports/qc-report') {
            this.getUsers()
            this.getEmployees()
            this.getProcesses()
            this.getQualityControls()
            this.getProducts()
        }

        if (pathname === '/admin/reports/team-status') {
            this.getTeams()
            this.getShifts()
        }

        if (pathname === '/admin/reports/fire-register') {
            this.getShifts()
        }
        if (pathname === '/admin/reports/work-analytics') {
            this.getShifts()
            this.getEmployees()
            this.getProcesses()
        }

        if (pathname === '/admin/reports/time-and-attendance-page') {
            this.getEmployees()
        }

        if (pathname === '/admin/reports/manufactured-blinds') {
            this.getQualityControls()
        }

        if (pathname === '/admin/reports/target-performance') {
            this.getEmployees()
            this.getCurrentUser()
        }
        if (pathname === '/admin/reports/who-works-here-page') {
            this.getEmployees()
        }

        this.checkPrivacy()
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

        getEmployees() {
            this.$API.Employee.getCleanEmployees()
            .then(res => {
                this.setEmployees(res.data)
            })
            .catch(err => {
                console.error(`Error: Global Employee Fetching Error`)
            })
        },

        getProcesses() {
            this.$API.Processes.getAll()
            .then(res => {
                this.setProcesses(res.data)
            })
            .catch(err => {
                console.error(`Error: Global Process Fetching Error`)
            })
        },

        getQualityControls() {
            axios.get(`/admin/quality-control/list`)
            .then(res => {
                this.setQualityControls(res.data.qualityControls)
            })
            .catch(err => {
                console.error(`Error: Global Quality Control Fetching Error`)
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

        getShifts() {
            axios.get(`/admin/shifts/all-shifts`)
            .then(res => {
                this.setShifts(res.data)
            })
            .catch(err => {
                console.error(`Error: Global Shifts Fetching Error`)
            })
        },

        getProducts() {
            axios.get(`/admin/orders/all-products`)
            .then(res => {
                console.log(res.data)
                this.setProducts(res.data)
            })
            .catch(err => {
                console.error(`Error: Global Products Fetching Error`)
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

        checkPrivacy() {
            let apiUrl = `/admin/users/check-privacy`

            axios.get(apiUrl)
            .then((response) => {
                this.setPrivacy(response.data)
            })
        },

        ...mapActions(['setUsers', 'setEmployees', 'setProcesses', 'setQualityControls', 'setTeams', 'setShifts', 'setProducts', 'setPrivacy', 'setUser'])
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
