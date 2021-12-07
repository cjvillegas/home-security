<template>
    <div>
        <global-page-header title="Exports"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">

            <div class="float-right">
                <el-button
                    type="primary"
                    @click="getList">
                    Reload
                </el-button>
            </div>

            <el-table
                fit
                :data="exports">
                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop"
                    :width="column.width ? column.width : ''">
                    <template slot-scope="scope">
                        <template v-if="['employee_full_name', 'user_name', 'process_name'].includes(column.prop)">
                            {{ scope.row[column.prop] | ucWords }}
                        </template>
                        <template v-else-if="column.prop === 'url'">
                            <el-link
                                v-if="scope.row.url"
                                :underline="false"
                                :href="scope.row.url"
                                type="success">
                                {{ scope.row.url | getFileNameFromPath }}
                            </el-link>
                            <span v-else>--:--</span>
                        </template>
                        <template v-else-if="column.prop === 'status'">
                            <span v-if="scope.row.status === 1" class="text-warning">In Progress</span>
                            <span v-if="scope.row.status === 2" class="text-success">Completed</span>
                            <span v-if="scope.row.status === 3" class="text-danger">Failed</span>
                            <span v-if="scope.row.status === 4" class="text-danger">Killed</span>
                        </template>
                        <template v-else-if="column.prop === 'type'">
                            {{ scope.row.type | cleanUpSnakeCaseWord }}
                        </template>
                        <template v-else-if="column.prop === 'created_at'">
                            {{ scope.row.created_at | fixDateTimeByFormat('MMM DD, YYYY hh:mm a') }}
                        </template>
                        <template v-else>
                            {{ scope.row[column.prop] }}
                        </template>
                    </template>
                </el-table-column>

                <el-table-column
                    label="Actions">
                    <template slot-scope="scope">
                        <el-popconfirm
                            @confirm="deleteExport(scope.row)"
                            confirm-button-text='OK'
                            cancel-button-text='No, Thanks'
                            icon="el-icon-info"
                            icon-color="red"
                            title="Are you sure to delete this?">
                            <el-button
                                type="text"
                                class="text-danger ml-2"
                                slot="reference">
                                <i class="fas fa-trash-alt"></i>
                            </el-button>
                        </el-popconfirm>
                    </template>
                </el-table-column>
            </el-table>

            <div class="text-right">
                <el-pagination
                    class="mt-3"
                    background
                    layout="total, sizes, prev, pager, next"
                    :total="pagination.total"
                    :page-size="pagination.size"
                    :page-sizes="[10, 25, 50, 100]"
                    :current-page="pagination.page"
                    @size-change="handleSize"
                    @current-change="handlePage">
                </el-pagination>
            </div>
        </el-card>
    </div>
</template>

<script>
    import pagination from "../../mixins/pagination";

    export default {
        name: "ExportsIndex",

        mixins: [pagination],

        data() {
            let columns = [
                {prop: 'id', label: 'ID', showOverflowTooltip: true, sortable: true, width: '80'},
                {prop: 'url', label: 'File', showOverflowTooltip: true, sortable: true},
                {prop: 'user_name', label: 'User', showOverflowTooltip: true, sortable: true},
                {prop: 'status', label: 'Status', showOverflowTooltip: true, sortable: true},
                {prop: 'type', label: 'Type', showOverflowTooltip: true, sortable: true},
                {prop: 'created_at', label: 'Exported At', showOverflowTooltip: true, sortable: true},
            ]

            return {
                loading: false,
                exports: [],
                columns: columns,
            }
        },

        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'

            this.getList()
        },

        methods: {
            getList() {
                this.loading = true

                this.$API.Exports.getExports()
                .then(res => {
                    this.exports = res.data.data
                    this.pagination.total = res.data.total
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            deleteExport(model) {
                this.loading = true

                this.$API.Exports.delete(model.id)
                .then(res => {
                    if (res.data) {
                        this.getList()
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            }
        }
    }
</script>
