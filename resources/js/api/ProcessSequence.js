import axios from 'axios'

export default {
    /**
     * API endpoint in fetching list of process sequence list
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.post(`/admin/process-sequence/get-list`, postData)
    },

    /**
     * API endpoint for fetching one single instance of process sequence.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    show(id) {
        return axios.get(`/admin/process-sequence/${id}`)
    },

    /**
     * API endpoint in storing new process sequence.
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    store(postData) {
        return axios.post(`/admin/process-sequence`, postData)
    },

    /**
     * API endpoint for updating a process sequence.
     * This route is under the API collection
     *
     * @param postData
     * @param id
     *
     * @return Promise
     */
    update(postData, id) {
        return axios.put(`/admin/process-sequence/${id}`, postData)
    },

    /**
     * API endpoint for deleting a process sequence.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/process-sequence/${id}`)
    }
}
