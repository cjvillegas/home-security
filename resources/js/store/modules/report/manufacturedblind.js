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
    async getBlinds({commit, state}, data) {
        let apiUrl = `/admin/reports/manufactured-blinds`

        commit('setLoading', true)
        await axios.post(apiUrl, data)
        .then((response) => {
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

    async exportManufacturedBlinds({commit}, data) {
        let apiUrl = `/admin/reports/export-manufactured-blind-report`

        commit('setLoading', true)
        await axios.post(apiUrl, data)
        .then((response) => {
            if (response.data.success) {
                this.$notify({
                    title: 'Manufactured Blind Report',
                    message: response.data.message,
                    type: 'success'
                })
            }
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
