import axios from "axios";

const state = {
    barcode: null,

    page: 'welcome',
    availableSlots: []
};

const getters = {
    barcode: state => state.barcode,
    page: state => state.page,
    availableSlots: state => state.availableSlots
};

const actions = {
    enterBarcode({commit, dispatch}, barcode) {
        commit('setBarcode', barcode)
        commit('setPage', 'bookingSlot')
    },

    getAvailableSlots({commit}) {
        let apiUrl = `/employee/overtime-bookings/available`

        axios.get(apiUrl)
        .then((res) => {
            commit('setAvailableSlots', res.data.slots)

        })
    }
};

const mutations = {
    setBarcode(state, barcode) {
        return state.barcode = barcode
    },
    setPage(state, page) {
        return state.page = page
    },
    setAvailableSlots(state, availableSlots) {
        return state.availableSlots = availableSlots
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
