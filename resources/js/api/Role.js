import axios from 'axios'

export default {
    /**
     * API endpoint in fetching list of Roles list
     * This route is under the API collection
     *
     * @param postData
     *
     * @return Promise
     */
    getList(postData) {
        return axios.post(`/admin/roles/list`, postData)
    },

    /**
     * API endpoint for fetching one list of permissions.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    getPermissions() {
        return axios.get(`/admin/roles/permissions`)
    },

    /**
     * API endpoint for Saving Role.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    save(postData) {
        return axios.post(`/admin/roles`, postData)
    },

    /**
     * API endpoint for Updating Role.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    update(postData) {
        return axios.patch(`/admin/roles/${postData.id}`, postData)
    },

    /**
     * API endpoint for DeletingRole.
     * This route is under the API collection
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/roles/${id}`)
    }
}
