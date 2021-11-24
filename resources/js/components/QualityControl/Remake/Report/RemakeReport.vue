<template>
    <div>
        <global-page-header title="QC Remake Reports"></global-page-header>
        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                 <div>
                    <el-input
                        v-model="filters.reportNumber"
                        clearable
                        placeholder="Search Report Number..."
                        @keyup.enter.native.prevent="searchReportNumber"
                        style="width: 250px">
                    </el-input>
                    <el-input
                        v-model="filters.orderNumber"
                        clearable
                        placeholder="Search Order Number..."
                        @keyup.enter.native.prevent="searchOrderNumber"
                        style="width: 250px">
                    </el-input>
                </div>
            </div>

            <el-table
                fit
                :data="orderRemakes">
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found.">
                    </el-empty>
                </template>
                <el-table-column
                    prop="report_no"
                    label="Report Number">
                </el-table-column>
                <el-table-column
                    label="Created Date"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.created_at | fixDateTimeByFormat('MMM DD, YYYY HH:mm:ss') }}
                    </template>
                </el-table-column>
                <el-table-column
                    prop="order_no"
                    label="Order Number">
                </el-table-column>
                <el-table-column
                    prop="user.name"
                    label="Validated By">
                </el-table-column>
                <el-table-column
                    label="Status"
                    prop="is_fully_verified"
                    show-overflow-tooltip>
                    <template slot-scope="scope">
                        <el-tag
                            size="mini"
                            :type="scope.row.is_fully_verified ? 'success' : 'danger'"
                            effect="dark">
                            {{ scope.row.is_fully_verified ? 'Fully Verified' : 'Partially Verified' }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column
                    width="100%"
                    label="Action"
                    class-name="table-action-button">
                    <template slot-scope="scope">
                        <template>
                            <el-button
                            @click="openViewDialog(scope.row)"
                                type="text"
                                class="ml-2 text-secondary">
                                <i class="fas fa-eye"></i>
                            </el-button>
                        </template>
                    </template>
                </el-table-column>
            </el-table>

            <el-pagination
                class="custom-pagination-class  mt-3 float-right"
                background
                layout="total, sizes, prev, pager, next"
                :total="filters.total"
                :page-size="filters.size"
                :page-sizes="[10, 25, 50, 100]"
                :current-page="filters.page"
                @size-change="handleSize"
                @current-change="handlePage">
            </el-pagination>
        </el-card>

        <remake-report-view-dialog
            :visible.sync="reportViewDialog"
            @close="closeForm">
        </remake-report-view-dialog>
    </div>
</template>
<style>
    .el-message-box {
        width: 40% !important;
    }
</style>
<script>
    import { mapActions, mapGetters, mapMutations } from 'vuex'
    import pagination from '../../../../mixins/pagination'
    export default {
        mixins: [pagination],
        data() {
            return {
                filters: {
                    reportNumber: null,
                    orderNumber: null,
                },
                reportViewDialog: false
            }
        },
        created() {
            this.filters.size = 30
            this.functionName = 'getOrderRemakes'
        },
        mounted() {
            this.getOrderRemakes(this.filters)


        },
        computed: {
            ...mapGetters('remakechecker', ['orderRemakes', 'orderRemakesTotal', 'loading']),
        },
        methods: {
            ...mapActions('remakechecker', ['getOrderRemakes']),
            ...mapMutations('remakechecker', ['setViewOrderRemake', 'setVerifiedBy']),
            searchReportNumber() {
                this.getOrderRemakes(this.filters)
            },
            searchOrderNumber() {
                this.getOrderRemakes(this.filters)
            },
            openViewDialog(data) {
                this.setViewOrderRemake(data)
                this.reportViewDialog = true
            },
            closeForm() {
                this.setViewOrderRemake([])
                this.reportViewDialog = false
            },
        },
        watch: {
            orderRemakesTotal: {
                handler(val) {
                    this.filters.total = val
                },
                immediate: true
            }
        }
    }
</script>
