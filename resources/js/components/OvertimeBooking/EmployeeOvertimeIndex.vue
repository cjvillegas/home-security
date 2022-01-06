<template>
    <div>
         <global-page-header title="Overtime List"></global-page-header>

         <el-card
            class="box-card mt-3"
            v-loading="loading">
            <div class="d-flex">
                <div>
                    <el-date-picker
                        v-model="filters.dateRange"
                        range-separator="~"
                        start-placeholder="Start date"
                        end-placeholder="End date"
                        :clearable="false"
                        type="daterange"
                        class="w-100">
                    </el-date-picker>

                </div>
                <el-button
                    @click="applyFilter"
                    type="default">
                    Apply Filter
                </el-button>
                <div class="ml-auto">
                    <el-button
                        v-print="'#employeeOvertimeTable'"
                        type="success">
                        <i class="fas fa-printer"></i> Print
                    </el-button>
                    <el-button
                        @click="openManualEntry"
                        type="primary">
                        <i class="fas fa-plus"></i> Add Confirmation
                    </el-button>
                </div>
            </div>
            <el-table
                id="employeeOvertimeTable"
                ref="multipleTable"
                :data="overtimeConfirmations"
                @selection-change="handleSelectionChange"
                class="w-100"
                fit>
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found">
                    </el-empty>
                </template>
                <el-table-column
                    prop="fullname"
                    label="Employee Fullname"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="available_date"
                    label="Confirmed Date"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="available_slots"
                    label="Available Slots"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="confirmed_slots"
                    label="Confirmed Slots"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="total_confirmed_hours"
                    label="Total Confirmed Hours"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="approved_hours"
                    label="Approved Hours"
                    sortable>
                </el-table-column>
                <el-table-column
                    width="100%"
                    label="Action"
                    class-name="table-action-button">
                    <template slot-scope="scope">
                        <template>
                            <el-tooltip
                                class="item"
                                effect="dark"
                                content="Edit"
                                placement="top"
                                :open-delay="1000">
                                <el-button
                                    @click="viewEmployeeOvertime(scope.row.id)"
                                    type="text">
                                    <i class="fas fa-eye"></i>
                                    View
                                </el-button>
                            </el-tooltip>
                        </template>
                    </template>
                </el-table-column>
            </el-table>
         </el-card>

         <employee-overtime-manual-entry-dialog
            :visible.sync="manualEntryDialog"
            @close="closeManualEntryDialog">
         </employee-overtime-manual-entry-dialog>

         <view-employee-overtime
            :employee="employeeOvertimeList"
            :visible.sync="employeeOvertimeDialog"
            @close="closeEmployeeOvertimeDialog">
         </view-employee-overtime>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
    export default {
        data() {
            return {
                filters: {
                    dateRange: []
                },
                manualEntryDialog: false,
                employeeOvertimeDialog: false,
                employeeOvertimeList: []
            }
        },

        created() {

        },

        computed: {
            ...mapGetters('overtimeBooking', ['overtimeConfirmations', 'loading'])
        },

        methods: {
            applyFilter() {
                if (this.filters.dateRange.length == 0) {
                    this.$notify({
                        title: 'Warning!',
                        message: 'Please select Date Range',
                        type: 'warning'
                    });

                    return
                }
                this.getOvertimeConfirmations(this.filters)
            },

            openManualEntry() {
                this.manualEntryDialog = true
            },

            viewEmployeeOvertime(id) {
                this.employeeViewId = id,
                this.employeeOvertimeDialog = true

                this.showEmployeeOvertimeList(id)
                .then((res) => {
                    this.employeeOvertimeList = res.data.employee
                })

            },

            handleSelectionChange() {

            },

            closeManualEntryDialog() {
                this.manualEntryDialog = false
            },

            closeEmployeeOvertimeDialog() {
                this.employeeOvertimeDialog = false
            },

            ...mapActions('overtimeBooking', ['getOvertimeConfirmations', 'showEmployeeOvertimeList'])
        }

    }
</script>
