import axios from 'axios'

export default {
    /**
     * API endpoint in fetching all Processes data
     *
     * @return Promise
     */
    getAll() {
        return axios.post(`/admin/processes/get-all`)
    }
}
