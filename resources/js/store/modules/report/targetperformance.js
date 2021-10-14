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
    getPerformances({commit, state}, data) {
        console.log('fetch backend data')
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
