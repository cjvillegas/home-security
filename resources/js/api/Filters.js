import axios from 'axios'

const BASE_URL = `/admin/filters`

export default {
    /**
     * API endpoint in storing a new filters
     * This route is under the API collection
     *
     * @handler Admin\FilterController@store
     *
     * @param postData
     *
     * @return Promise
     */
    store(postData) {
        return axios.post(BASE_URL, postData)
    },

    /**
     * API endpoint in updating a filter instance
     * This route is under the API collection
     *
     * @handler Admin\FilterController@update
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
     * API endpoint to retrieve a filter instance
     * This route is under the API collection
     *
     * @handler Admin\FilterController@show
     *
     * @param id
     *
     * @return Promise
     */
    show(id) {
        return axios.get(`${BASE_URL}/${id}`)
    },

    /**
     * API endpoint to retrieve a filter instance by its type
     * This route is under the API collection
     *
     * @handler Admin\FilterController@getByType
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
