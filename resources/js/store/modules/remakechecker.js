import axios from "axios";

const state = {
    orders: [],
    selectedOrderNo: null,
    selectedBlindId: [],
    blindValidationData: [{}],
    answeredQuestion: [],
    activeForm: 'search',

    orderRemakeResponse: [],
    loading: false,

    //reports
    orderRemakes: [],
    viewOrderRemake: []
};

const getters = {
    orders: state => state.orders,
    selectedOrderNo: state => state.selectedOrderNo,
    selectedBlindId: state => state.selectedBlindId,
    blindValidationData: state => state.blindValidationData,
    answeredQuestion: state => state.answeredQuestion,
    activeForm: state => state.activeForm,

    orderRemakeResponse: state => state.orderRemakeResponse,
    loading: state => state.loading,

    //reports
    orderRemakes: state => state.orderRemakes,
    viewOrderRemake: state => state.viewOrderRemake
};

const actions = {
    async getOrders({commit}, data) {
        let apiUrl = `/admin/remake-checker/get-orders`
        commit('setLoading', true)

        return await axios.post(apiUrl, data)
            .then((res) => {
                if (res.data.orders.length > 0) {
                    commit('setOrders', res.data.orders)
                    commit('setSelectedOrderNo', data.order_no)

                    commit('setActiveForm', 'ordersList')
                    commit('setLoading', false)
                }
            })
            .catch((err) => {
                console.log(err)
            })
            .finally(() => {
                commit('setLoading', false)
            })
    },

    async saveOrderRemake({commit}, data) {
        let apiUrl = `/admin/remake-checker`
        commit('setLoading', true)
        await axios.post(apiUrl, data)
            .then((res) => {
                console.log(res.data.orderRemake)
                commit('setOrderRemakeResponse', res.data.orderRemake)
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
    },


    //reports
    async getOrderRemakes({commit}, data) {
        let apiUrl = `/admin/remake-report/get-list`
        commit('setLoading', true)
        await axios.post(apiUrl, data)
            .then((res) => {
                commit('setOrderRemakes', res.data.orderRemakes)
            })
            .catch((err) => {
                console.log(err)
            })
            .finally(() => {
                commit('setLoading', false)
            })
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
    setBlindValidationData(state, blindValidationData) {
        return state.blindValidationData = blindValidationData
    },
    setAnsweredQuestions(state, answeredQuestions) {
        return state.answeredQuestion = answeredQuestions
    },
    setActiveForm(state, activeForm) {
        return state.activeForm = activeForm
    },
    setOrderRemakeResponse(state, orderRemakeResponse) {
        return state.orderRemakeResponse = orderRemakeResponse
    },
    setLoading(state, loading) {
        return state.loading = loading
    },

    //reports
    setOrderRemakes(state, orderRemakes) {
        return state.orderRemakes = orderRemakes
    },
    setViewOrderRemake(state, viewOrderRemake) {
        return state.viewOrderRemake = viewOrderRemake
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
