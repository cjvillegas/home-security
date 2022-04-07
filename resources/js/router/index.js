/*
* This page will handle all the Vue routing within the application
*
* @author Chaps
*/

// Vue and Vue Router
import Vue from 'vue'
import VueRouter from 'vue-router'

// Register Vue Router
Vue.use(VueRouter)

// User Components
const UserIndex = () => import('../components/User/UserIndex')
const UserList = () => import('../components/User/UserList')
const UserView = () => import('../components/User/UserView')

// Monitoring
const MonitoringIndex = () => import('../components/Monitoring/MonitoringIndex')

export default new VueRouter({
    linkActiveClass: 'active',
    linkExactActiveClass: '',
    scrollBehavior () {
        return { x: 0, y: 0 }
    },
    routes: [
        {
            path: '/user-index',
            name: 'User Index',
            component: UserIndex,
            props: true,
            children: [
                {
                    path: 'list',
                    name: 'User List',
                    component: UserList,
                    props: true,
                },
                {
                    path: ':id',
                    name: 'User View',
                    component: UserView,
                    props: true,
                }
            ]
        },
        {
            path: '/monitoring-index',
            name: "Monitoring Index",
            component: MonitoringIndex,
            props: true,
            children: []
        }
    ]
})
