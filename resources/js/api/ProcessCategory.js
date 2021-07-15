import axios from 'axios'

export default {
    /**
     * API endpoint in fetching list of process category list
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.post(`/admin/settings/process-category/get-list`, postData)
    },

    /**
     * API endpoint in storing new process category.
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    store(postData) {
        return axios.post(`/admin/settings/process-category`, postData)
    },

    /**
     * API endpoint for updating a process category.
     * This route is under the API collection
     *
     * @param postData
     * @param id
     *
     * @return Promise
     */
    update(postData, id) {
        return axios.put(`/admin/settings/process-category/${id}`, postData)
    },

    /**
     * API endpoint for deleting a process category.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/settings/process-category/${id}`)
    }


}
