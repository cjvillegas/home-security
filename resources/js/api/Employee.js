import axios from "axios";

export default {
    /**
     * API endpoint in fetching list of employee list
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.post(`/admin/employees/get-list`, postData)
    },

    /**
     * API endpoint for fetching one single instance of employee.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    show(id) {
        return axios.get(`/admin/employees/${id}`)
    },

    /**
     * API endpoint in storing new employee.
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    store(postData) {
        return axios.post(`/admin/employees`, postData)
    },

    /**
     * API endpoint for updating an employee.
     * This route is under the API collection
     *
     * @param postData
     * @param id
     *
     * @return Promise
     */
    update(postData, id) {
        return axios.put(`/admin/employees/${id}`, postData)
    },

    /**
     * API endpoint for deleting an employee.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/employees/${id}`)
    },

    /**
     * API endpoint for changing the status of an employee.
     *
     * @param id
     *
     * @return Promise
     */
    changeStatus(id) {
        return axios.patch(`/admin/employees/${id}/status-change`)
    }
}
