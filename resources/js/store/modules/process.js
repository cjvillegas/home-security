import axios from "axios";

const state = {
    processes: []
};

const getters = {
    processes: state => state.processeses
};

const actions = {
    async searchProcessesList({state, commit}, filters) {
        let apiUrl = `/admin/processes/search`

        return await axios.post(apiUrl, filters)
    }
};

const mutations = {
    setProcesses(state, processes) {
        return state.processes = processes
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
