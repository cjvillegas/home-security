import axios from 'axios'

export default {
    /**
     * Fetch work analytics report based on the provided SOD and EOD
     *
     * @param start
     * @param end
     *
     * @return Promise
     */
    fetchWorkAnalytics(start, end) {
        return axios.get(`/admin/reports/work-analytics/get-work-analytics?start=${start}&end=${end}`)
    },

    /**
     * Fetch hourly report analytics
     *
     * @param start
     * @param end
     *
     * @return Promise
     */
    manufacturedBlindsAnalytics() {
        return axios.get(`/admin/reports/work-analytics/manufactured-blinds-analytics`)
    },
}
