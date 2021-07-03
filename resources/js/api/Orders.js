import axios from 'axios'

export default {
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
     * @param orderNo
     *
     * @return Promise
     */
    getOrderDetails(orderNo) {
        return axios.get(`/admin/orders/${orderNo}/order-list`);
    }
}
