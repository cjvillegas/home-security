import axios from 'axios'

export default {
    /**
     * API endpoint the exports the hourly analytics report to an excel file.
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
     * API endpoint the exports the daily analytics report to an excel file.
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
    }
}
