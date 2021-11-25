import axios from "axios";

const state = {
    customers: [],
    customersTotal: 0,

    loading: false,
};

const getters = {
    customers: state => state.customers,
    customersTotal: state => state.customersTotal,
    loading: state => state.loading
};

const actions = {
    fetchCustomers({commit}, data) {
        let apiUrl = `/admin/customers/list`

        axios.post(apiUrl, data)
        .then((response) => {
            commit('setCustomers', response.data.customers.data)
            commit('setCustomersTotal', response.data.customers.total)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {
            commit('setLoading', false)
        })
    },

    createNewCustomer({commit}, data) {
        let apiUrl = `/admin/customers`

        commit('setLoading', true)
        return axios.post(apiUrl, data)
    },

    updateCustomer({commit}, data) {
        let apiUrl = `/admin/customers/${data.id}`

        return axios.patch(apiUrl, data)
    },

    deleteCustomer({commit}, id){
        let apiUrl = `/admin/customers/${id}`

        return axios.delete(apiUrl)
    }
};

const mutations = {
    setCustomers(state, customers) {
        return state.customers = customers
    },
    setCustomersTotal(state, customersTotal) {
        return state.customersTotal = customersTotal
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
