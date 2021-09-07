import axios from 'axios'

export default {
    /**
     * API endpoint in fetching all Processes data
     *
     * @return Promise
     */
    getAll() {
        return axios.post(`/admin/processes/get-all`)
    },

    /**
     * API endpoint in fetching list of process list
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.post(`/admin/processes/get-list`, postData)
    },

    /**
     * API endpoint for fetching one single instance of a process.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    show(id) {
        return axios.get(`/admin/processes/${id}`)
    },

    /**
     * API endpoint in storing new a process.
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    store(postData) {
        return axios.post(`/admin/processes`, postData)
    },

    /**
     * API endpoint for updating a process.
     * This route is under the API collection
     *
     * @param postData
     * @param id
     *
     * @return Promise
     */
    update(postData, id) {
        return axios.put(`/admin/processes/${id}`, postData)
    },

    /**
     * API endpoint for deleting a process.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/processes/${id}`)
    },
}
