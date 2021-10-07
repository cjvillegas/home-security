import axios from 'axios'

let basePath = `/admin/reports/work-analytics`

export default {
    /**
     * Fetch work analytics report based on the provided SOD and EOD
     *
     * @param params
     *
     * @handler \App\Http\Controllers\Admin\Report\WorkAnalyticsReportController@getWorkAnalytics
     *
     * @return Promise
     */
    fetchWorkAnalytics(params) {
        return axios.get(`${basePath}/get-work-analytics`, {params: params})
    },

    /**
     * Fetch manufactured blind analytics. Data being fetched is from the previous day
     * and the current day.
     *
     * @handler \App\Http\Controllers\Admin\Report\WorkAnalyticsReportController@manufacturedBlindsAnalytics
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
     * @handler \App\Http\Controllers\Admin\Report\WorkAnalyticsReportController@getDespatchDepartmentAnalytics
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
     * @handler \App\Http\Controllers\Admin\Report\ReportController@getQcList
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
     * @handler \App\Http\Controllers\Admin\Report\ReportController@exportQcFaultData
     *
     * @returns Promise
     */
    exportQcFaultData(params) {
        return axios.get(`/admin/reports/export-qc-fault-data`, {
            params: params
        })
    },

    /**
     * Fetch QC Tag for reporting
     *
     * @param params
     *
     * @handler \App\Http\Controllers\Admin\Report\ReportController@getTeamStatusReport
     *
     * @returns Promise
     */
    getTeamStatusList(params) {
        return axios.get(`/admin/reports/team-status-list`, {
            params: params
        })
    },

    /**
     * Exports the team status report
     *
     * @param params
     *
     * @handler \App\Http\Controllers\Admin\Report\ReportController@exportTeamStatus
     *
     * @returns Promise
     */
    exportTeamStatusReport(params) {
        return axios.get(`/admin/reports/export-team-status-report`, {
            params: params
        })
    },

    /**
     * API endpoint that fetches timeclock data for employees
     *
     * @param params
     *
     * @handler \App\Http\Controllers\Admin\Report\ReportController@timeAndAttendance
     *
     * @returns Promise
     */
    timeAndAttendance(params) {
        return axios.get(`/admin/reports/time-and-attendance`, {
            params
        })
    },

    /**
     * Exports the time and attendance report to excel file
     *
     * @param params
     *
     * @handler \App\Http\Controllers\Admin\Report\ReportController@exportTimeAndAttendance
     *
     * @returns Promise
     */
    exportTimeAndAttendance(params) {
        return axios.get(`/admin/reports/export-time-and-attendance`, {
            params: params
        })
    },

    /**
     * API endpoint that fetches the who works here data
     *
     * @param params
     *
     * @handler \App\Http\Controllers\Admin\Report\ReportController@whoWorksHere
     *
     * @returns Promise
     */
    whoWorksHere(params) {
        return axios.get(`/admin/reports/who-works-here`, {params})
    },

    dashboardMachineStatistics(params) {
        return axios.get(`/admin/reports/dashboard-machine-statistics`, {params})
    }
}
