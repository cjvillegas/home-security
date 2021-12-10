import axios from "axios";

const state = {
    slots: []
};

const getters = {
    slots: state => state.slots
};

const actions = {
    getSlots({commit}, data) {
        let apiUrl = `/admin/overtime-bookings`

        axios.post(apiUrl)
        .then((res) => {
            commit('setBookingSlots', res.data.slots)
        })
        .catch(err => {
            console.log(err)
        })
        .finally(_ => {

        })
    },

    saveSlot({commit}, data) {
        let apiUrl = `/admin/overtime-bookings/save`

        return axios.post(apiUrl, data)
    },

    toggleLockSlot({commit}, data) {
        let apiUrl = `/admin/overtime-bookings/toggle-lock`

        return axios.patch(apiUrl, data)
    }
};

const mutations = {
    setBookingSlots(state, slots) {
        return state.slots = slots
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
