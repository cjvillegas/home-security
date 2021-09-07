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

// Order Components
const OrderList = () => import('../components/Orders/OrderIndex')
const OrderView = () => import('../components/Orders/OrderView')

// Dashboard Components
const DashboardIndex = () => import('../components/Dashboard/DashboardIndex')

// Machine Components
const MachineIndex = () => import('../components/Machines/MachineIndex')

// Employee Components
const EmployeeIndex = () => import('../components/Employee/EmployeeIndex')
const EmployeeList = () => import('../components/Employee/EmployeeList')
const EmployeeView = () => import('../components/Employee/EmployeeView')

// User Components
const UserIndex = () => import('../components/User/UserIndex')
const UserList = () => import('../components/User/UserList')
const UserView = () => import('../components/User/UserView')

// Notification Components
const NotificationIndex = () => import('../components/Notifications/NotificationIndex')
const NotificationList = () => import('../components/Notifications/NotificationList')
const NotificationView = () => import('../components/Notifications/NotificationView')

// Processes Components
const ProcessIndex = () => import('../components/Process/ProcessIndex')
const ProcessList = () => import('../components/Process/ProcessList')
const ProcessView = () => import('../components/Process/ProcessView')

export default new VueRouter({
    linkActiveClass: 'active',
    linkExactActiveClass: '',
    scrollBehavior () {
        return { x: 0, y: 0 }
    },
    routes: [
        {
            path: '/list',
            name: 'Order List',
            component: OrderList,
            props: true
        },
        {
            path: '/order-list/:toSearch',
            name: 'Order View',
            component: OrderView,
            props: true
        },
        {
            path: '/dashboard',
            name: 'Dashboard Index',
            component: DashboardIndex,
            props: true
        },
        {
            path: '/employee-index',
            name: 'Employee Index',
            component: EmployeeIndex,
            props: true,
            children: [
                {
                    path: 'list',
                    name: 'Employee List',
                    component: EmployeeList,
                    props: true,
                },
                {
                    path: ':id',
                    name: 'Employee View',
                    component: EmployeeView,
                    props: true,
                }
            ]
        },
        {
            path: '/process-index',
            name: 'Process Index',
            component: ProcessIndex,
            props: true,
            children: [
                {
                    path: 'list',
                    name: 'Process List',
                    component: ProcessList,
                    props: true,
                },
                {
                    path: ':id',
                    name: 'Process View',
                    component: ProcessView,
                    props: true,
                }
            ]
        },
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
            path: '/notification-index',
            name: 'Notification Index',
            component: NotificationIndex,
            props: true,
            children: [
                {
                    path: 'list',
                    name: 'Notification List',
                    component: NotificationList,
                    props: true,
                },
                {
                    path: ':id',
                    name: 'Notification View',
                    component: NotificationView,
                    props: true,
                },
            ]
        }
    ]
})
