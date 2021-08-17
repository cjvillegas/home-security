import axios from 'axios'

export default {
    /**
     * API endpoint in fetching list of notifications
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.get(`/admin/notifications/get-list`, {
            params: postData
        })
    },

    /**
     * API endpoint for fetching one single instance of notification.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    show(id) {
        return axios.get(`/admin/notifications/${id}`)
    },

    /**
     * API endpoint for deleting a notification.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/notifications/${id}`)
    },

}
