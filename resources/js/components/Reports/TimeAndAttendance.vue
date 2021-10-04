<template>
    <div>
        <global-page-header title="Time & Attendance"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div class="ml-auto">
                    <global-filter-box>
                        <label>Select Date</label>
                        <el-date-picker
                            v-model="filters.date"
                            type="date"
                            placeholder="Select Dates"
                            class="w-100">
                        </el-date-picker>

                        <global-employee-selector
                            class="mt-3"
                            :value.sync="filters.employees"
                            is-multiple>
                        </global-employee-selector>

                        <el-button
                            @click="getList"
                            :disabled="disableApplyFilterButton"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>

                    <el-button
                        @click="exportReport"
                        :disabled="!canExportData"
                        type="success">
                        <i class="fas fa-file-export"></i> Export
                    </el-button>

                    <el-button
                        @click="print"
                        :disabled="!canExportData"
                        type="info">
                        <i class="fas fa-print"></i> Print
                    </el-button>
                </div>
            </div>

            <el-table
                fit
                :data="timeclocks">
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                    </el-empty>
                </template>

                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop"
                    :width="column.width ? column.width : ''">
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from "../../mixins/pagination"

    export default {
        name: "TimeAndAttendance",

        mixins: [pagination],

        data() {
            let columns = [
                {label: 'Employee Name', prop: 'employee_name', showOverflowTooltip: true, sortable: true},
                {label: 'Clock No.', prop: 'clock_number', showOverflowTooltip: true, sortable: true},
                {label: 'Clock In', prop: 'clock_in', showOverflowTooltip: true, sortable: true},
                {label: 'Clock Out', prop: 'clock_out', showOverflowTooltip: true, sortable: true},
                {label: 'Time In', prop: 'time_in', showOverflowTooltip: true, sortable: true},
            ]

            return {
                loading: false,
                filters: {
                    employees: [null],
                    date: moment().subtract(1, 'days').format('YYYY-MM-DD')
                },
                timeclocks: [],
                columns: columns
            }
        },

        computed: {
            ...mapGetters(['users']),

            canExportData() {
                return !!(this.timeclocks && this.timeclocks.length > 1)
            },

            disableApplyFilterButton() {
                return !this.filters.date
            }
        },

        created() {
            // check the pagination mixins for the implementation
            this.functionName = 'getList'
        },

        methods: {
            getList() {
                this.loading = true

                let filters = cloneDeep(this.filters)

                this.sanitizeFilter(filters)

                this.$API.Reports.timeAndAttendance(filters)
                .then(res => {
                    this.timeclocks = res.data
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            exportReport() {
                this.loading = true

                let filters = cloneDeep(this.filters)

                this.sanitizeFilter(filters)

                this.$API.Reports.exportTimeAndAttendance(filters)
                .then(res => {
                    if (res.data.success) {
                        this.$notify({
                            title: 'Team Status Report',
                            message: res.data.message,
                            type: 'success'
                        })
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            print() {
                let baseUrl = window.location.origin

                let content = "<html><head>"
                content += `<link href="${baseUrl}/css/print.css" rel="stylesheet" />`

                content += "<table><thead><tr>"
                for (let x of this.columns) {
                    content += `<th>${x.label}</th>`
                }
                content += "</tr></thead>"

                content += "<tbody>"
                for (let item of this.timeclocks) {
                    content += '<tr>'
                    for (let x of this.columns) {
                        console.log(item)
                        content += `<td>${item[x.prop]}</td>`
                    }
                    content += '<tr>'
                }
                content += "</tbody></table>"

                content += "</head></html>"

                let win = window.open("")

                win.document.write(content)
                win.document.close()
                win.print()
            },

            sanitizeFilter(filters) {
                if (filters.employees.every(e => e === null)) {
                    delete filters.employees
                }
            }
        }
    }
</script>
