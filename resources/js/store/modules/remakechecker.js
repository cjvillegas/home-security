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
    orderRemakesTotal: 0,
    viewOrderRemake: [],

    //email
    emails: []
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
    orderRemakesTotal: state => state.orderRemakesTotal,
    viewOrderRemake: state => state.viewOrderRemake,

    //email
    emails: state => state.emails
};

const actions = {
    getOrders({commit}, data) {
        let apiUrl = `/admin/remake-checker/get-orders`
        commit('setLoading', true)

        return axios.post(apiUrl, data)
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

    saveOrderRemake({commit}, data) {
        let apiUrl = `/admin/remake-checker`
        commit('setLoading', true)
        axios.post(apiUrl, data)
            .then((res) => {
                commit('setOrderRemakeResponse', res.data.orderRemake)
                commit('setLoading', false)
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
    getOrderRemakes({commit}, data) {
        let apiUrl = `/admin/remake-report/get-list`
        commit('setLoading', true)
        axios.post(apiUrl, data)
            .then((res) => {
                commit('setOrderRemakes', res.data.orderRemakes.data)
                commit('setOrderRemakesTotal', res.data.orderRemakes.total)
            })
            .catch((err) => {
                console.log(err)
            })
            .finally(() => {
                commit('setLoading', false)
            })
    },

    //email
    getEmails({commit}, data) {
        let apiUrl = `/admin/email/get-list`
        commit('setLoading', true)
        axios.post(apiUrl, data)
            .then((res) => {
                commit('setEmails', res.data.emails)
            })
            .catch((err) => {
                console.log(err)
            })
            .finally(() => {
                commit('setLoading', false)
            })
    },

    storeEmail({commit}, data) {
        let apiUrl = `/admin/email/store`
        commit('setLoading', true)
        axios.post(apiUrl, data)
            .then((res) => {

            })
            .catch((err) => {
                console.log(err)
            })
            .finally(() => {
                commit('setLoading', false)
            })
    },

    deleteEmail({commit, dispatch}, id) {
        let apiUrl = `/admin/${id}/destroy`
        commit('setLoading', true)
        axios.delete(apiUrl)
            .then((res) => {

            })
            .catch((err) => {
                console.log(err)
            })
            .finally(() => {
                commit('setLoading', false)
                dispatch('getEmails', {})
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
    setOrderRemakesTotal(state, orderRemakesTotal) {
        return state.orderRemakesTotal = orderRemakesTotal
    },
    setViewOrderRemake(state, viewOrderRemake) {
        return state.viewOrderRemake = viewOrderRemake
    },

    //email
    setEmails(state, emails) {
        return state.emails = emails
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
