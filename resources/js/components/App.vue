<template>
    <div class="app">
        <router-view :page-data="pageData"></router-view>
    </div>
</template>

<script>
export default {
    name: 'App',
    props: {
        pageData: {
            default: () => {}
        }
    },
    data() {
        return {

        }
    },
    created() {
        this.routeChecker()
    },
    methods: {
        routeChecker() {
            let pathname = window.location.pathname
            let hash = window.location.hash

            console.log(pathname, hash)

            if (pathname === '/admin/teams' && hash === '#/') {
                this.$router.push({name: 'Team List', params: {}})
            }

            if (pathname === '/admin/notifications' && hash === '#/') {
                this.$router.push({name: 'Notification List', params: {}})
            }

            if (pathname === '/admin/users' && hash === '#/') {
                this.$router.push({name: 'User List', params: {}})
            }

            if (pathname === '/admin/employees' && hash === '#/') {
                this.$router.push({name: 'Employee List', params: {}})
            }

            if (pathname === '/admin/processes' && hash === '#/') {
                this.$router.push({name: 'Process List', params: {}})
            }

            if (pathname === '/admin/orders' && hash === '#/') {
                this.$router.push({name: 'Order List', params: {}})
            }

            if (pathname === '/admin/reports/work-analytics' && hash === '#/') {
                this.$router.push({name: 'Work Analytics', params: {}})
            }

            if (pathname === '/admin/settings' && hash === '#/') {
                this.$router.push({name: 'Settings Index', params: {}})
            }

            if (pathname === '/admin' && hash === '#/') {
                this.$router.push({name: 'Dashboard Index', params: {}})
            }
        }
    }
}
</script>

<style>
.fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}

/* Enter and leave animations can use different */
/* durations and timing functions.              */
.slide-fade-enter-active {
    transition: all .3s ease;
}
.slide-fade-leave-active {
    transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
    /* .slide-fade-leave-active below version 2.1.8 */ {
    transform: translateX(10px);
    opacity: 0;
}
</style>
