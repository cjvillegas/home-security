import axios from 'axios'
import { update } from 'lodash'

export default {
    /**
     * API endpoint in fetching list of Shift list
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.post(`/admin/shifts/list`, postData)
    },

    /**
     * API endpoint for Saving shift.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    save(postData) {
        return axios.post(`/admin/shifts`, postData)
    },

    /**
     * API endpoint for Updating shift.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    update(postData) {
        return axios.patch(`/admin/shifts/${postData.id}`, postData)
    },

    /**
     * API endpoint for Deleting shift.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/shifts/${id}`)
    }
}
