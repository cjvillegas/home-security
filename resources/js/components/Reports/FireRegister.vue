<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Fire Register</h4>
        </el-card>
        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div class="ml-auto">
                    <global-filter-box>
                        <div>
                            <label for="date">Select Date</label>
                            <el-date-picker
                                v-model="filters.date"
                                placeholder="Pick a day"
                                :clearable="false"
                                type="date"
                                class="w-100">
                            </el-date-picker>
                        </div>

                        <global-shift-selector
                            class="mt-3"
                            :value.sync="filters.shifts"
                            :isMultiple="false">
                        </global-shift-selector>

                        <el-button
                            @click="selectShift"
                            :disabled="disableApplyFilterButton"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>
                    <el-button
                        @click="clickExportData"
                        :disabled="!canExportData"
                        type="success">
                        <i class="fas fa-file-export"></i> Export
                    </el-button>
                </div>
            </div>
            <el-table
                fit
                :data="employees">
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                    </el-empty>
                </template>

                <el-table-column
                    prop="fullname"
                    label="Employee name"
                    sortable>
                </el-table-column>

                <el-table-column
                    label="Operation Started At"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.scannedtime | fixDateTimeByFormat('MMM DD, YYYY HH:mm:ss') }}
                    </template>
                </el-table-column>

                <el-table-column
                    prop="clock_num"
                    label="Clock Num"
                    sortable>
                </el-table-column>

                <el-table-column
                    label="Clock In"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.clock_in | fixDateByFormat('MMM DD, YYYY HH:mm:ss') }}
                    </template>
                </el-table-column>

                <el-table-column
                    label="Clock Out"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.clock_out | fixDateByFormat('MMM DD, YYYY HH:mm:ss') }}
                    </template>
                </el-table-column>

                <el-table-column
                    prop="time_in"
                    label="Time In (H:mm:ss)"
                    sortable>
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import { mapActions, mapGetters } from 'vuex'

    export default {
        name: "FireRegister",
        data() {
            return {
                data: {
                },
                filters: {
                    shifts: null,
                    from: null,
                    to: null,
                    date: null
                }
            }
        },

        computed: {
            ...mapGetters(['shifts']),
            ...mapGetters('fireregister', ['employees', 'loading']),

            disableApplyFilterButton() {
                return this.filters.shifts == null
            },

            canExportData() {
                return this.employees.length > 0
            }
        },

        methods: {
            ...mapActions('fireregister', ['getEmployeesList', 'exportFireRegister']),

            selectShift() {
                if (this.filters.shifts == 1) {
                    this.filters.from = '06:00:00'
                    this.filters.to = '14:00:00'
                }
                if (this.filters.shifts == 2) {
                    this.filters.from = '14:00:00'
                    this.filters.to = '22:00:00'
                }
                if (this.filters.shifts == 3) {
                    this.filters.from = '22:00:00'
                    this.filters.to = '06:00:00'
                }
                let filters = cloneDeep(this.filters)

                this.getEmployeesList(filters)
            },

            clickExportData() {
                this.exportFireRegister(this.filters)
                .then(() => {
                    this.$notify({
                        title: 'Success',
                        message: 'Your data is being exported. Please wait a while and check the Export page for your export',
                        type: 'success'
                    })
                })
            },
        },

    }
</script>
