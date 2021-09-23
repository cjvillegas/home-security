import axios from "axios";

const state = {
    scanners: [],
    order_no: null,
    loading: false
};

const getters = {
    scanners: state => state.scanners,
    order_no: state => state.order_no,
    loading: state => state.loading
};

const actions = {
    async getScannersData({state, commit}, order_no) {
        let apiUrl = `/admin/scanners/get-scanners-by-barcode`

        await axios.post(apiUrl, {order_no: order_no})
        .then((response) => {
            console.log(response.data.scanners)
            commit('setScanners', response.data.scanners)
        })
        .catch((err) => {
            console.log(err)
        })

    },

    async updateProductType({commit}, data) {
        let apiUrl = `/admin/orders/${data.order_id}/update-product-type`

        return await axios.patch(apiUrl, data)
    },

    setOrderNo({commit}, order_no) {
        commit('setOrderNo', order_no)
    }
};

const mutations = {
    setScanners(state, scanners) {
        return state.scanners = scanners
    },
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
