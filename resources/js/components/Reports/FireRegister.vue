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
                    <!-- <el-button
                        @click="exportFireRegister"
                        :disabled="!canExportData"
                        type="success">
                        <i class="fas fa-file-export"></i> Export
                    </el-button> -->
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
                    label="Scanned Date and Time"
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
            </el-table>
        </el-card>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
    import pagination from "../../mixins/pagination";

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
                this.getEmployeesList(this.filters)
            }
        },

    }
</script>
