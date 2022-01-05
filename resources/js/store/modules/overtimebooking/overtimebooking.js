import axios from "axios";

const state = {
    slots: [],
    availableSlots: [],
    overtimeConfirmations: [],
    overtimeRequests: [],
    selectedOvertimeRequests: [],
    slotsTotal: 0,

    loading: false
};

const getters = {
    slots: state => state.slots,
    availableSlots: state => state.availableSlots,
    overtimeConfirmations: state => state.overtimeConfirmations,
    overtimeRequests: state => state.overtimeRequests,
    selectedOvertimeRequests: state => state.selectedOvertimeRequests,
    slotsTotal: state => state.slotsTotal,

    loading: state => state.loading
};

const actions = {
    getSlots({commit}, data) {
        let apiUrl = `/admin/overtime-bookings`

        commit('setLoading', true)
        axios.post(apiUrl, data)
        .then((res) => {
            commit('setBookingSlots', res.data.slots.data)
            commit('setSlotsTotal', res.data.slots.total)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {
            commit('setLoading', false)
        })
    },

    saveSlot({commit}, data) {
        let apiUrl = `/admin/overtime-bookings/save`

        return axios.post(apiUrl, data)
    },

    toggleLockSlot({commit}, id) {
        let apiUrl = `/admin/overtime-bookings/${id}/toggle-lock`

        return axios.patch(apiUrl)
    },

    deleteSlot({commit}, id) {
        let apiUrl = `/admin/overtime-bookings/${id}/delete`

        return axios.delete(apiUrl)
    },

    // Overtime Confirmations

    getOvertimeConfirmations({commit}, data) {
        let apiUrl = `/admin/overtime-bookings/confirmations`

        commit('setLoading', true)
        axios.post(apiUrl, data)
        .then((res) => {
            commit('setOvertimeConfirmations', res.data.confirmations)
            commit('setLoading', false)
        })
    },

    showEmployeeOvertimeList({commit}, id) {
        let apiUrl = `/admin/overtime-bookings/show-employee-overtimes`
        let data = {
            'employeeId': id
        }

        return axios.post(apiUrl, data)
    },

    getAllSlots({commit}) {
        let apiUrl = `/admin/overtime-bookings/get-all-slots`

        axios.get(apiUrl)
        .then((res) => {
            commit('setAvailableSlots', res.data.availableSlots)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {

        })
    },

    saveEmployeeOvertime({commit}, data) {
        let apiUrl = `/admin/overtime-bookings/save-manual-entry`

        return axios.post(apiUrl, data)
    },

    getEmployeeOvertimeRequests({commit}, data) {
        let apiUrl = `/admin/overtime-bookings/requests`

        commit('setLoading', true)
        axios.post(apiUrl, data)
        .then((res) => {
            commit('setOvertimeRequests', res.data.requests)
            commit('setLoading', false)
        })
    },

    updateEmployeeOvertimeRequests({commit}, data) {
        let apiUrl = `/admin/overtime-bookings/requests/update`

        commit('setLoading', true)
        return axios.post(apiUrl, data)
    }
};

const mutations = {
    setBookingSlots(state, slots) {
        return state.slots = slots
    },
    setAvailableSlots(state, availableSlots) {
        return state.availableSlots = availableSlots
    },
    setOvertimeConfirmations(state, overtimeConfirmations) {
        return state.overtimeConfirmations = overtimeConfirmations
    },
    setOvertimeRequests(state, overtimeRequests) {
        return state.overtimeRequests = overtimeRequests
    },
    setSelectedOvertimeRequests(state, selectedOvertimeRequests) {
        return state.selectedOvertimeRequests = selectedOvertimeRequests
    },
    setSlotsTotal(state, slotsTotal) {
        return state.slotsTotal = slotsTotal
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
