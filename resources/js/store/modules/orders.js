import axios from "axios";

const state = {
    order_no: null,
    loading: false
};

const getters = {
    order_no: state => state.order_no,
    loading: state => state.loading
};

const actions = {
    async updateProductType({commit}, data) {
        let apiUrl = `/admin/orders/${data.order_id}/update-product-type`

        return await axios.patch(apiUrl, data)
    },

    setOrderNo({commit}, order_no) {
        commit('setOrderNo', order_no)
    }
};

const mutations = {
    setOrderNo(state, order_no) {
        return state.order_no = order_no
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
