import axios from 'axios'

export default {
    /**
     * Fetch hourly report analytics
     *
     * @param start
     * @param end
     *
     * @return Promise
     */
    fetchHourlyAnalytics(start, end) {
        return axios.get(`/admin/reports/work-analytics/get-hourly-analytics?start=${start}&end=${end}`)
    },

    /**
     * Fetch daily report analytics
     *
     * @param start
     * @param end
     *
     * @return Promise
     */
    fetchDailyAnalytics(start, end) {
        return axios.get(`/admin/reports/work-analytics/get-daily-analytics?start=${start}&end=${end}`)
    },
}
