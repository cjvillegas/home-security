import axios from "axios";

const state = {
    shiftPerformances: [],

    shiftPerformanceView: false,
    form: {},
    loading: false
};

const getters = {
    shiftPerformances: state => state.shiftPerformances,
    shiftPerformanceView: state => state.shiftPerformanceView,
    form: state => state.form,
    loading: state => state.loading
};

const actions = {
    runShiftPerformanceReport({commit}, data) {
        let apiUrl = `/admin/reports/shift-performance/get-report`
        commit('setLoading', true)
        axios.post(apiUrl, data)
        .then((res) => {
            commit('setShiftPerformance', res.data.shiftPerformances)

            commit('setShiftPerformanceView', true)
            commit('setLoading', false)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {
            commit('setLoading', false)
        })
    },

    exportShiftPerformances({commit}, data) {
        let apiUrl = `/admin/reports/export-shift-performance`

        commit('setLoading', true)
        axios.post(apiUrl, data)
        .then((res) => {
            commit('setLoading', false)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {
            commit('setLoading', false)
        })
    },

    backToFilters({commit}) {
        commit('setShiftPerformance', []),
        commit('setShiftPerformanceView', false)
    }
};

const mutations = {
    setShiftPerformance(state, shiftPerformances) {
        return state.shiftPerformances = shiftPerformances
    },

    setShiftPerformanceView(state, shiftPerformanceView) {
        return state.shiftPerformanceView = shiftPerformanceView
    },
    setForm(state, form) {
        return state.form = form
    },
    setLoading(state, loading) {
        return state.loading = loading
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
