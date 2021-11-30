import axios from "axios";

const state = {

};

const getters = {

};

const actions = {
    runShiftPerformanceReport({commit}, data) {
        let apiUrl = `/admin/reports/shift-performance/get-report`

        axios.post(apiUrl, data)
    }
};

const mutations = {

};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
