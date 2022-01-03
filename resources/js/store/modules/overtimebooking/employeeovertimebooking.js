import axios from "axios";

const state = {
    barcode: null,
    employee: {},
    page: 'welcome',
    availableSlots: [],
    selectedSlots: [],
    employeeConfirmedSlots: [],

    loading: false,
};

const getters = {
    barcode: state => state.barcode,
    employee: state => state.employee,
    page: state => state.page,
    availableSlots: state => state.availableSlots,
    selectedSlots: state => state.selectedSlots,
    employeeConfirmedSlots: state => state.employeeConfirmedSlots,

    loading: state => state.loading
};

const actions = {
    enterBarcode({commit, dispatch}, barcode) {
        let apiUrl = `/barcode`
        let payload = {
            'barcode': barcode
        }
        commit('setLoading', true)
        return axios.post(apiUrl, payload)
    },

    getAvailableSlots({commit}) {
        let apiUrl = `/overtime-bookings/available`

        commit('setLoading', true)
        axios.get(apiUrl)
        .then((res) => {
            commit('setAvailableSlots', res.data.slots)
            commit('setLoading', false)
        })
    },

    saveSelectedSlots({commit}, data) {
        let apiUrl = `/overtime-bookings/store-selected-slots`

        return axios.post(apiUrl, data)
    },

    reset({commit}) {
        commit('setBarcode', null)
        commit('setEmployee', {})
        commit('setAvailableSlots', [])
        commit('setSelectedSlots', [])
        commit('setEmployeeConfirmedSlots', [])

        commit('setLoading', true)
        setTimeout(_ => {
            commit('setPage', 'welcome')
            commit('setLoading', false)
        }, 1000)
    }
};

const mutations = {
    setBarcode(state, barcode) {
        return state.barcode = barcode
    },
    setEmployee(state, employee) {
        return state.employee = employee
    },
    setPage(state, page) {
        return state.page = page
    },
    setAvailableSlots(state, availableSlots) {
        return state.availableSlots = availableSlots
    },
    setSelectedSlots(state, selectedSlots) {
        return state.selectedSlots = selectedSlots
    },
    //will use this to check whether the Employee has already confirmed
    //displayed on page
    setEmployeeConfirmedSlots(state, employeeConfirmedSlots) {
        return state.employeeConfirmedSlots = employeeConfirmedSlots
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
