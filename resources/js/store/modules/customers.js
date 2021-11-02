import axios from "axios";

const state = {
    customers: [],

    loading: false,
};

const getters = {
    customers: state => state.customers,
    loading: state => state.loading
};

const actions = {
    async fetchCustomers({commit}, data) {
        let apiUrl = `/admin/customers/list`

        await axios.post(apiUrl, data)
        .then((response) => {
            commit('setCustomers', response.data.customers)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {
            commit('setLoading', false)
        })
    },

    async createNewCustomer({commit}, data) {
        let apiUrl = `/admin/customers`

        commit('setLoading', true)
        return await axios.post(apiUrl, data)
    },

    async updateCustomer({commit}, data) {

    },

    async deleteCustomer({commit}, data){

    }
};

const mutations = {
    setCustomers(state, customers) {
        return state.customers = customers
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
