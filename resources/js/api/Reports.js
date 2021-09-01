import axios from 'axios'

let basePath = `/admin/reports/work-analytics`

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
        return axios.get(`${basePath}/get-work-analytics?start=${start}&end=${end}`)
    },

    /**
     * Fetch manufactured blind analytics. Data being fetched is from the previous day
     * and the current day.
     *
     * @return Promise
     */
    manufacturedBlindsAnalytics() {
        return axios.get(`${basePath}/manufactured-blinds-analytics`)
    },

    /**
     * Fetch despatch department analytics. Data being fetched is from the previous day
     * and the current day.
     *
     * @return Promise
     */
    getDespatchDepartmentAnalytics() {
        return axios.get(`${basePath}/despatch-department-analytics`)
    },

    /**
     * Fetch QC Tag for reporting
     *
     * @param params
     *
     * @returns Promise
     */
    getQcList(params) {
        return axios.get(`/admin/reports/qc-list`, {
            params: params
        })
    },

    /**
     * Exports the qc fault data based on the passed array
     *
     * @param params
     *
     * @returns Promise
     */
    exportQcFaultData(params) {
        return axios.get(`/admin/reports/export-qc-fault-data`, {
            params: params
        })
    }
}
