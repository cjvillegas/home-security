import axios from 'axios'

const BASE_URL = "/admin/in-house/purchase-orders"

export default {
    /**
     * Fetch purchase orders
     *
     * @handler Admin\InHouse\PurchaseOrderController@index
     *
     * @return Promise
     */
    fetchPurchaseOrders(params) {
        return axios.get(`${BASE_URL}`, {
            params
        })
    }
}
