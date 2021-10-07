import axios from "axios";

const state = {
    blinds: [],

    loading: false
};

const getters = {
    blinds: state => state.blinds,
    loading: state => state.loading
};

const actions = {
    getBlinds({commit, state}, data) {
        let apiUrl = `/admin/reports/manufactured-blinds`

        axios.post(apiUrl, data)
        .then((response) => {
            console.log(response.data)
            commit('setLoading', false)
            commit('setBlinds', response.data.blinds)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {
            commit('setLoading', false)
        })
    },

    exportManufacturedBlinds() {

    }
};

const mutations = {
    setBlinds(state, blinds) {
        return state.blinds = blinds
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
