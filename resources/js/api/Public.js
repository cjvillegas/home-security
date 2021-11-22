import axios from 'axios'

const PUBLIC_DASHBOARD = '/public/dashboard'

export default {
    /**
     * Retrieve public dashboard data
     *
     * @param params
     *
     * @returns Promise
     */
    getDashboardData(params) {
        return axios.get(`${PUBLIC_DASHBOARD}/orders-data`, {
            params
        })
    }
}

