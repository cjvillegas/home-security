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
                            v-model="filters.dates"
                            type="daterange"
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

            <div class="text-right">
                <el-pagination
                    class="mt-3"
                    background
                    layout="total, sizes, prev, pager, next"
                    :total="filters.total"
                    :page-size="filters.size"
                    :page-sizes="[10, 25, 50, 100]"
                    :current-page="filters.page"
                    @size-change="handleSize"
                    @current-change="handlePage">
                </el-pagination>
            </div>
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
                {label: 'Clock No.', prop: 'clock_num', showOverflowTooltip: true, sortable: true},
                {label: 'Clock In', prop: 'clock_in', showOverflowTooltip: true, sortable: true},
                {label: 'Clock Out', prop: 'clock_out', showOverflowTooltip: true, sortable: true},
                {label: 'Time In', prop: 'time_in', showOverflowTooltip: true, sortable: true},
            ]

            return {
                loading: false,
                filters: {
                    employees: [null],
                    dates: null
                },
                timeclocks: [],
                columns: columns
            }
        },

        computed: {
            ...mapGetters(['users']),

            canExportData() {

            },

            disableApplyFilterButton() {
                return !this.filters.dates || !this.filters.dates.length
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

                this.$API.Reports.timeclockEmployees(filters)
                .then(res => {
                    this.timeclocks = res.data.data
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            exportReport() {

            },

            sanitizeFilter(filters) {
                if (filters.employees.every(e => e === null)) {
                    delete filters.employees
                }
            }
        }
    }
</script>
