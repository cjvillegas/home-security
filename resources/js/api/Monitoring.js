import axios from "axios"

export default {
    /**
     * Get all monitorings.
     *
     * @handler Controllers\Admin\MonitoringController@list
     *
     * @return Promise
     */
    list() {
        return axios.get(`/admin/monitorings/list`)
    },

    /**
     * API endpoint for a new monitoring.
     *
     * @handler Controllers\Admin\MonitoringController@store
     *
     * @param postData - object
     *
     * @return Promise
     */
    store(postData) {
        return axios.post(`/admin/monitorings`, postData)
    },


    /**
     * API endpoint to update a monitoring.
     *
     * @handler Controllers\Admin\MonitoringController@store
     *
     * @param id - int
     * @param postData - object
     *
     * @return Promise
     */
    update(id, postData) {
        return axios.patch(`/admin/monitorings/${id}`, postData)
    },

    /**
     * API endpoint to delete a monitoring.
     *
     * @handler Controllers\Admin\MonitoringController@delete
     *
     * @param id - int
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/monitorings/${id}`)
    },
}
