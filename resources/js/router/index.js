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
                    path: '/employee-index',
                    name: 'Employee List',
                    component: EmployeeList,
                    props: true,
                },
                {
                    path: '/:id',
                    name: 'Employee View',
                    component: EmployeeView,
                    props: true,
                }
            ]
        },
    ]
})
