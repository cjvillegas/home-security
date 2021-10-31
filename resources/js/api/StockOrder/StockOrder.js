import axios from 'axios'

const BASE_URL = '/admin/in-house/stock-orders'
const ORDER_LINE_URL = '/admin/in-house/stock-order-items'

export default {
    /**
     * Create new order. The order's status will be draft.
     *
     * @handler Admin\InHouse\StockInventoryController@store
     *
     * @return Promise
     */
    createStockOrder() {
        return axios.post(`${BASE_URL}`)
    },

    /**
     * Create new order. The order's status will be draft.
     *
     * @param id
     *
     * @handler Admin\InHouse\StockInventoryController@show
     *
     * @return Promise
     */
    showStockOrder(id) {
        return axios.get(`${BASE_URL}/${id}`)
    },

    /**
     * Update the stock order.
     *
     * @param postData
     * @param id
     *
     * @handler Admin\InHouse\StockInventoryController@update
     *
     * @return Promise
     */
    updateStockOrder(postData, id) {
        return axios.put(`${BASE_URL}/${id}`, postData)
    },

    /**
     * Delete a stock order.
     *
     * @param id
     *
     * @handler Admin\InHouse\StockInventoryController@delete
     *
     * @return Promise
     */
    deleteStockOrder(id) {
        return axios.delete(`${BASE_URL}/${id}`)
    },

    /**
     * Approve a stock order
     *
     * @param id
     *
     * @handler Admin\InHouse\StockInventoryController@approveOrder
     *
     * @return Promise
     */
    approveOrder(id) {
        return axios.patch(`${BASE_URL}/approve/${id}`)
    },

    /**
     * Clone a stock order to a new order
     *
     * @param id
     *
     * @handler Admin\InHouse\StockInventoryController@cloneOrder
     *
     * @return Promise
     */
    cloneOrder(id) {
        return axios.patch(`${BASE_URL}/clone/${id}`)
    },

    /**
     * Cancel a stock order
     *
     * @param id
     *
     * @handler Admin\InHouse\StockInventoryController@cancelStockOrder
     *
     * @return Promise
     */
    cancelOrder(id) {
        return axios.patch(`${BASE_URL}/cancel/${id}`)
    },

    /**
     * Get draft orders
     *
     * @param filters
     *
     * @handler Admin\InHouse\StockInventoryController@draftOrders
     *
     * @return Promise
     */
    fetchDraftOrders(filters) {
        return axios.get(`${BASE_URL}/draft`)
    },

    /**
     * Get pending orders
     *
     * @param filters
     *
     * @handler Admin\InHouse\StockInventoryController@pendingOrders
     *
     * @return Promise
     */
    fetchPendingOrders(filters) {
        return axios.get(`${BASE_URL}/pending`)
    },

    /**
     * Get approved orders
     *
     * @param filters
     *
     * @handler Admin\InHouse\StockInventoryController@approvedOrders
     *
     * @return Promise
     */
    fetchApprovedOrders(filters) {
        return axios.get(`${BASE_URL}/approved`)
    },

    /**
     * Get draft orders total count
     *
     * @handler Admin\InHouse\StockInventoryController@countDraftOrders
     *
     * @return Promise
     */
    fetchDraftTotalCount() {
        return axios.get(`${BASE_URL}/draft/total-count`)
    },

    /**
     * Get pending orders
     *
     * @param filters
     *
     * @handler Admin\InHouse\StockInventoryController@countPendingOrders
     *
     * @return Promise
     */
    fetchPendingTotalCount(filters) {
        return axios.get(`${BASE_URL}/pending/total-count`)
    },

    /**
     * Get pending orders
     *
     * @param filters
     *
     * @handler Admin\InHouse\StockInventoryController@countApprovedOrders
     *
     * @return Promise
     */
    fetchApprovedTotalCount(filters) {
        return axios.get(`${BASE_URL}/approved/total-count`)
    },

    /**
     * O R D E R  L I N E
     */

    /**
     * Create new order line.
     *
     * @param postData
     *
     * @handler Admin\InHouse\StockOrderItemController@store
     *
     * @return Promise
     */
    addNewOrderLine(postData) {
        return axios.post(`${ORDER_LINE_URL}`, postData)
    },

    /**
     * Create new order line.
     *
     * @param postData
     * @param id
     *
     * @handler Admin\InHouse\StockOrderItemController@update
     *
     * @return Promise
     */
    updateOrderLine(postData, id) {
        return axios.put(`${ORDER_LINE_URL}/${id}`, postData)
    },

    /**
     * Delete an order line.
     *
     * @param id
     *
     * @handler Admin\InHouse\StockOrderItemController@delete
     *
     * @return Promise
     */
    deleteOrderLine(id) {
        return axios.delete(`${ORDER_LINE_URL}/${id}`)
    },

    /**
     * Move order lines from one order to the other
     *
     * @param postData
     *
     * @handler Admin\InHouse\StockOrderItemController@moveItems
     *
     * @return Promise
     */
    moveItemsToNewOrder(postData) {
        return axios.post(`${ORDER_LINE_URL}/move-to-new-order`, postData)
    },

    /**
     * Move order lines from one order to the other
     *
     * @param postData
     *
     * @handler Admin\InHouse\StockOrderItemController@moveItems
     *
     * @return Promise
     */
    moveItems(postData) {
        return axios.post(`${ORDER_LINE_URL}/move`, postData)
    }
}
