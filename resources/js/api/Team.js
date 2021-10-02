import axios from 'axios'

export default {
    /**
     * API endpoint in fetching list of team list
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.post(`/admin/teams/list`, postData)
    },

    /**
     * API endpoint for Saving Team.
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    save(postData) {
        return axios.post(`/admin/teams`, postData)
    },

    /**
     * API endpoint for Updating Team.
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    update(postData) {
        return axios.patch(`/admin/teams/${postData.id}`, postData)
    },

    /**
     * API endpoint for Deleting Team.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/teams/${id}`)
    }
}
