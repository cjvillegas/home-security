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

            if (pathname === '/admin/users' && hash === '#/') {
                this.$router.push({name: 'User List', params: {}})
            }

            if (pathname === '/admin/monitorings' && hash === '#/') {
                this.$router.push({name: 'Monitoring Index', params: {}})
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
