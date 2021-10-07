import { register } from "numeral";
import Vue from "vue";
import Vuex from 'vuex'

//modules
import orders from './modules/orders'
import process from './modules/process'
import fireregister from './modules/fireregister'
import manufacturedblinds from './modules/report/manufacturedblinds'

Vue.use(Vuex)

export default new Vuex.Store({
    strict: true,
    state: {
        users: [],
        employees: [],
        processes: [],
        qualityControls: [],
        teams: [],
        shifts: []
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
        }
    },

    modules: {
        manufacturedblinds,
        orders,
        process,
        fireregister
    }
})
