import axios from 'axios'

export default {
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
        return axios.get(`/admin/orders/${toSearch}/order-list?field=${field}`);
    }
}