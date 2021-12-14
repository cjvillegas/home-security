import axios from 'axios'

export default {
    /**
     * Fetch order list
     *
     * @param params
     *
     * @handler Admin\OrdersController@orderList
     *
     * @return Promise
     */
    orderList(params) {
        return axios.get(`/admin/orders/order-list`, {
            params: params
        })
    },

    /**
     * Export orders to a CSV file
     *
     * @param params
     *
     * @handler Admin\OrdersController@exportOrders
     *
     * @return Promise
     */
    exportOrders(params) {
        return axios.get(`/admin/orders/export-orders`, {
            params: params
        })
    },

    /**
     * Import orders directly from Blind Data
     *
     * @param field
     * @param value
     *
     * @handler Admin\OrdersController@importFromBlind
     *
     * @return Promise
     */
    importFromBlind(field, value) {
        return axios.get(`/admin/orders/import-from-blind?value=${value}&field=${field}`)
    },

    /**
     * API endpoint that searches and order based on the provided field
     * the field parameter is a DB column and the search string is the
     * search query
     *
     * @return Promise
     */
    searchOrderByField(field, searchString) {
        return axios.get(`/admin/orders/search-orders-by-field?field=${field}&searchString=${searchString}`)
    },

    /**
     * Main API endpoint in fetching all the orders
     *
     * @param size
     * @param page
     * @param searchString
     *
     * @return Promise
     */
    fetch(size, page, searchString) {
        let query = size ? `size=${size}` : ''
        query += page ? `${query ? '&' : ''}page=${page}` : ''
        query += searchString ? `${query ? '&' : ''}searchString=${searchString}` : ''

        return axios.get(`/admin/orders/fetch?${query}`)
    },

    /**
     * Retrieve orders with same order_no
     *
     * @param toSearch
     * @param field
     *
     * @return Promise
     */
    getOrderDetails(field, toSearch) {
        return axios.get(`/admin/orders/${toSearch}/order-details?field=${field}`);
    },

    /**
     * Retrieve orders' scanners data based on the provided order no
     *
     * @param orderNo
     * @param params
     *
     * @returns Promise
     */
    getOrderScannersData(orderNo, params) {
        return axios.get(`/admin/orders/${orderNo}/scanners`, {
            params
        })
    },

    /**
     * Get orders' planned work based on the passed order no
     *
     * @param orderNo
     *
     * @returns Promise
     */
    getOrderPlannedWork(orderNo) {
        return axios.get(`/admin/orders/${orderNo}/planned-work`)
    },

    getOrderProcessSequences(orderNo) {
        return axios.get(`/admin/orders/${orderNo}/process-sequences`)
    },

    getOrdersByOrderNo(orderNo) {
        return axios.get(`/admin/orders/${orderNo}/order-list-by-order-no`)
    }
}
