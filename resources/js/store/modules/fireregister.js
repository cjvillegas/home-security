import axios from "axios";

const state = {
    employees: [],

    loading: false
};

const getters = {
    employees: state => state.employees,
    loading: state => state.loading
};

const actions = {
    getEmployeesList({state, commit}, data) {
        let apiUrl = `/admin/reports/fire-register`
        commit('setLoading', true)

        axios.post(apiUrl, data)
        .then((response) => {
            commit('setLoading', false)
            commit('setEmployees', response.data.employees)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {
            commit('setLoading', false)
        })
    },

    exportFireRegister() {

    }
};

const mutations = {
    setEmployees(state, employees) {
        return state.employees = employees
    },
    setLoading(state, loading) {
        return state.loading = loading
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
