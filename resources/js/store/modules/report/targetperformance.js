import axios from "axios";

const state = {
    performances: [],

    loading: false
};

const getters = {
    performances: state => state.performances,
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
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
