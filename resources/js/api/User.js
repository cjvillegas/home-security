import axios from "axios";

export default {
    /**
     * API endpoint in fetching list of user list
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.post(`/admin/users/get-list`, postData)
    },

    /**
     * API endpoint for fetching one single instance of user.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    show(id) {
        return axios.get(`/admin/users/${id}`)
    },

    /**
     * API endpoint in storing new user.
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    store(postData) {
        return axios.post(`/admin/users`, postData)
    },

    /**
     * API endpoint for updating a user.
     * This route is under the API collection
     *
     * @param postData
     * @param id
     *
     * @return Promise
     */
    update(postData, id) {
        return axios.put(`/admin/users/${id}`, postData)
    },

    /**
     * API endpoint for deleting a user.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/users/${id}`)
    },

    /**
     * API endpoint for changing the status of an user.
     *
     * @param id
     *
     * @return Promise
     */
    changeStatus(id) {
        return axios.patch(`/admin/users/${id}/status-change`)
    },

    /**
     * API endpoint by getting the current authenticated user
     *
     * @return Promise
     */
    getAuthUser() {
        return axios.get(`/admin/users/get-auth-user`)
    }
}
