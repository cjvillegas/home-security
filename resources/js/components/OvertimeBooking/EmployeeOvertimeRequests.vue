<template>
    <div>
        <global-page-header title="Overtime Requests"></global-page-header>

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
                        v-show="overtimeRequests.length > 0"
                        @click="confirm"
                        type="primary">
                        <i class="fa fa-check-square"></i> Action
                    </el-button>
                </div>
            </div>

            <el-table
                ref="multipleTable"
                :data="overtimeRequests"
                @selection-change="handleSelectionChange"
                class="w-100"
                fit>
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                    </el-empty>
                </template>
                <el-table-column
                    type="selection"
                    width="55">
                </el-table-column>
                <el-table-column
                    prop="barcode"
                    label="Employee Barcode"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="fullname"
                    label="Employee Fullname"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="available_date"
                    label="Date"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.available_date | fixDateTimeByFormat('MMM DD, YYYY') }}
                    </template>
                </el-table-column>
                <el-table-column
                    prop="available_date"
                    label="Working Day"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.available_date | fixDateTimeByFormat('dddd') }}
                    </template>
                </el-table-column>
                <el-table-column
                    prop="department"
                    label="Department"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.department | ucWords }}
                    </template>
                </el-table-column>
                <el-table-column
                    prop="shift"
                    label="Shift"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.shift | ucWords }}
                    </template>
                </el-table-column>
                <el-table-column
                    prop="is_approved"
                    label="Status"
                    sortable>
                    <template slot-scope="scope">
                        <el-tag
                            v-if="scope.row.is_approved"
                            size="mini"
                            type="success"
                            effect="dark">
                            Approved
                        </el-tag>
                        <el-tag
                            v-if="!scope.row.is_approved && scope.row.rejected_at != null"
                            size="mini"
                            type="danger"
                            effect="dark">
                            Rejected
                        </el-tag>
                        <el-tag
                            v-if="!scope.row.is_approved && scope.row.rejected_at == null"
                            size="mini"
                            type="info"
                            effect="dark">
                            Waiting for Confirmation
                        </el-tag>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <confirm-dialog
            :visible.sync="confirmDialog"
            :date-range="filters.dateRange"
            @close="closeForm">
        </confirm-dialog>
    </div>
</template>

<script>
    import { mapActions, mapGetters, mapMutations } from 'vuex'
    export default {
        data() {
            return {
                filters: {
                    dateRange: null
                },

                confirmDialog: false
            }
        },

        computed: {
            ...mapGetters('overtimeBooking', ['overtimeRequests', 'loading', 'selectedOvertimeRequests'])
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
                this.getEmployeeOvertimeRequests(this.filters)
            },

            handleSelectionChange(item) {
                this.setSelectedOvertimeRequests(item)
            },

            confirm() {
                this.confirmDialog = true
                console.log('zz')
            },

            closeForm() {
                this.confirmDialog = false
            },

            ...mapActions('overtimeBooking', ['getEmployeeOvertimeRequests']),
            ...mapMutations('overtimeBooking', ['setSelectedOvertimeRequests'])
        }
    }
</script>
