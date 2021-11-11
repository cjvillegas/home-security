import axios from "axios";

const state = {
    orders: [],
    selectedOrderNo: null,
    selectedBlindId: [],

    activeForm: 'search',

    loading: false
};

const getters = {
    orders: state => state.orders,
    selectedOrderNo: state => state.selectedOrderNo,
    selectedBlindId: state => state.selectedBlindId,

    activeForm: state => state.activeForm,

    loading: state => state.loading
};

const actions = {
    async getOrders({commit}, data) {
        let apiUrl = `/admin/remake-checker/get-orders`
        commit('setLoading', true)

        return await axios.post(apiUrl, data)
        .then((res) => {
            console.log(res.data)
            if (res.data.orders.length > 0) {
                commit('setOrders', res.data.orders)
                commit('setSelectedOrderNo', data.order_no)

                commit('setActiveForm', 'ordersList')
            }
        })
        .catch((err) => {
            console.log(err)
        })
        .finally(() => {
            commit('setLoading', false)
        })
    },

    backToMainScreen({commit}) {
        //back to Main Screen
        commit('setActiveForm', 'search')
        //resetting all values
        commit('setOrders', [])
        commit('setSelectedOrderNo', null)
        commit('setSelectedBlindId', [])
    }
};

const mutations = {
    setOrders(state, orders) {
        return state.orders = orders
    },
    setSelectedOrderNo(state, selectedOrderNo) {
        return state.selectedOrderNo = selectedOrderNo
    },
    setSelectedBlindId(state, selectedBlindId) {
        return state.selectedBlindId = selectedBlindId
    },
    setActiveForm(state, activeForm) {
        return state.activeForm = activeForm
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
