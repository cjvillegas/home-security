import axios from "axios";

const state = {
    performances: [],
    dates: [],
    loading: false
};

const getters = {
    performances: state => state.performances,
    dates: state => state.dates,
    loading: state => state.loading
};

const actions = {
    async getPerformances({commit, state}, data) {
        let apiUrl = `/admin/reports/target-performance`

        commit('setLoading', true)
        await axios.post(apiUrl, data)
        .then((response) => {
            commit('setLoading', false)
            commit('setPerformances', response.data.performances)
            commit('setDates', response.data.dates)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {
            commit('setLoading', false)
        })
    }
};

const mutations = {
    setLoading(state, loading) {
        return state.loading = loading
    },
    setPerformances(state, performances) {
        return state.performances = performances
    },
    setDates(state, dates) {
        return state.dates = dates
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
