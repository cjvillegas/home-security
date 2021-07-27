import axios from 'axios'

export default {

     /**
     * Main API endpoint in fetching all Machines
     *
     * @return Promise
     */
    fetch(data) {
        let apiUrl = `/admin/machines/machines-list?page=${data.page}&size=${data.size}`

        return axios.get(apiUrl)
    },

    /**
     * Save Machine API
     *
     * @return Promise
     */
    save(data) {
        let apiUrl = `/admin/machines/store`

        return axios.post(apiUrl, data)
    }
}