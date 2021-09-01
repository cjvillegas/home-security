import Vue from "vue";
import Vuex from 'vuex'


Vue.use(Vuex)

export default new Vuex.Store({
    strict: true,
    state: {
        users: [],
        employees: [],
        processes: [],
        qualityControls: []
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
        }
    }
})
