<template>
    <div>
        <global-page-header title="QC Remake Reports"></global-page-header>
        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                 <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search employees..."
                        @keyup.enter.native.prevent="getOrderRemakes(filters)"
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
                    prop="blind_id"
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
                            @click="openViewDialog(scope.row.validated_blinds)"
                                type="text"
                                class="ml-2 text-secondary">
                                <i class="fas fa-eye"></i>
                            </el-button>
                        </template>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <remake-report-view-dialog
            :visible.sync="reportViewDialog"
            @close="closeForm">
        </remake-report-view-dialog>
    </div>
</template>

<script>
    import { mapActions, mapGetters, mapMutations } from 'vuex'
    export default {
        data() {
            return {
                filters: {},

                reportViewDialog: false
            }
        },
        created() {
            this.getOrderRemakes(this.filters)
        },
        computed: {
            ...mapGetters('remakechecker', ['orderRemakes', 'loading']),
        },
        methods: {
            ...mapActions('remakechecker', ['getOrderRemakes']),
            ...mapMutations('remakechecker', ['setViewOrderRemake']),
            openViewDialog(data) {
                this.setViewOrderRemake(data)
                this.reportViewDialog = true
            },
            closeForm() {
                this.setViewOrderRemake([])
                this.reportViewDialog = false
            },
        }
    }
</script>
