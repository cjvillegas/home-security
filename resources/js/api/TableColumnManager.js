import axios from 'axios'

const BASE_URL = `/admin/table-column-managers`

export default {
    /**
     * API endpoint in storing a new column manager
     * This route is under the API collection
     *
     * @handler Admin\TableColumnManagerController@store
     *
     * @param postData
     *
     * @return Promise
     */
    store(postData) {
        return axios.post(BASE_URL, postData)
    },

    /**
     * API endpoint in updating a column manager instance
     * This route is under the API collection
     *
     * @handler Admin\TableColumnManagerController@update
     *
     * @param postData
     * @param id
     *
     * @return Promise
     */
    update(postData, id) {
        return axios.put(`${BASE_URL}/${id}`, postData)
    },

    /**
     * API endpoint to retrieve a column manager instance instance
     * This route is under the API collection
     *
     * @handler Admin\TableColumnManagerController@show
     *
     * @param id
     *
     * @return Promise
     */
    show(id) {
        return axios.get(`${BASE_URL}/${id}`)
    },

    /**
     * API endpoint to retrieve a column manager instance by its type
     * This route is under the API collection
     *
     * @handler Admin\TableColumnManagerController@getByType
     *
     * @param type
     *
     * @return Promise
     */
    showByType(type) {
        return axios.get(`${BASE_URL}/get-by-type`, {
            params: {
                type: type
            }
        })
    }
}
