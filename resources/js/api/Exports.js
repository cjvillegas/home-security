import axios from 'axios'

export default {
    /**
     * API endpoint that exports the hourly analytics report to an excel file.
     * This endpoint will return a blob that will be parsed and handled by our frontend
     *
     * @param headers
     * @param data
     *
     * @return Promise
     */
    exportHourlyWorkAnalyticsReport(headers, data) {
        return axios({
            method: 'post',
            url: `/admin/exports/work-analytics/export-hourly-work-analytics-report`,
            responseType: 'blob',
            data: {
                headers,
                data
            }
        })
    },

    /**
     * API endpoint that exports the daily analytics report to an excel file.
     * This endpoint will return a blob that will be parsed and handled by our frontend
     *
     * @param headers
     * @param data
     *
     * @return Promise
     */
    exportDailyWorkAnalyticsReport(headers, data) {
        return axios({
            method: 'post',
            url: `/admin/exports/work-analytics/export-daily-work-analytics-report`,
            responseType: 'blob',
            data: {
                headers,
                data
            }
        })
    },

    /**
     * API endpoint that exports raw scanners data based on the given daterange.
     * This endpoint will return a blob that will be parsed and handled by our frontend.
     *
     * @param start
     * @param end
     *
     * @return Promise
     */
    exportRawData(start, end) {
        return axios.get(`/admin/exports/export-raw-scanners-data?start=${start}&end=${end}`)
    },

    /**
     * API endpoint that exports raw scanners data based on the given daterange.
     * This endpoint will return a blob that will be parsed and handled by our frontend.
     *
     * @param params
     *
     * @return Promise
     */
    getExports(params) {
        return axios.get(`/admin/exports/export-list`, {
            params
        })
    },

    /**
     * API endpoint for deleting an export.
     *
     * @param id
     *
     * @return Promise
     */
    delete(id) {
        return axios.delete(`/admin/exports/${id}`)
    }
}
