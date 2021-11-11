import { register } from "numeral";
import Vue from "vue";
import Vuex from 'vuex'

//modules
import orders from './modules/orders'
import process from './modules/process'
import fireregister from './modules/fireregister'
import manufacturedblind from './modules/report/manufacturedblind'
import targetperformance from './modules/report/targetperformance'
import remakechecker from './modules/remakechecker'

Vue.use(Vuex)

export default new Vuex.Store({
    strict: true,
    state: {
        users: [],
        employees: [],
        processes: [],
        qualityControls: [],
        teams: [],
        shifts: [],
        products: [],
        user: {},
        privacy: false
    },
    getters: {
        users(state) {
            return state.users
        },
        employees(state) {
            return state.employees
        },
        processes(state) {
            return state.processes
        },
        qualityControls(state) {
            return state.qualityControls
        },
        teams(state) {
            return state.teams
        },
        shifts(state) {
            return state.shifts
        },
        products(state) {
            return state.products
        },
        user(state) {
            return state.user
        },
        privacy(state) {
            return state.privacy
        }
    },
    mutations: {
        SET_USERS(state, users) {
            state.users = users
        },
        SET_EMPLOYEES(state, employees) {
            state.employees = employees
        },
        SET_PROCESSES(state, processes) {
            state.processes = processes
        },
        SET_QUALITY_CONTROLS(state, qualityControls) {
            state.qualityControls = qualityControls
        },
        SET_TEAMS(state, teams) {
            state.teams = teams
        },
        SET_SHIFTS(state, shifts) {
            state.shifts = shifts
        },
        SET_PRODUCTS(state, products) {
            state.products = products
        },
        SET_USER(state, user) {
            state.user = user
        },
        SET_PRIVACY(state, privacy) {
            state.privacy = privacy
        }
    },
    actions: {
        setUsers({ commit }, users) {
            commit('SET_USERS', users)
        },
        setEmployees({ commit }, employees) {
            commit('SET_EMPLOYEES', employees)
        },
        setProcesses({ commit }, processes) {
            commit('SET_PROCESSES', processes)
        },
        setQualityControls({ commit }, qualityControls) {
            commit('SET_QUALITY_CONTROLS', qualityControls)
        },
        setTeams({commit}, teams) {
            commit('SET_TEAMS', teams)
        },
        setShifts({commit}, shifts) {
            commit('SET_SHIFTS', shifts)
        },
        setProducts({commit}, products) {
            commit('SET_PRODUCTS', products)
        },
        setUser({commit}, user) {
            commit('SET_USER', user)
        },
        setPrivacy({commit}, privacy) {
            commit('SET_PRIVACY', privacy)
        }
    },

    modules: {
        orders,
        process,
        fireregister,
        manufacturedblind,
        targetperformance,
        remakechecker
    }
})
