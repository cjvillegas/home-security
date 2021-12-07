<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Notification List</h4>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search notifications..."
                        @keyup.enter.native.prevent="getList"
                        style="width: 250px">
                    </el-input>
                </div>
            </div>

            <el-table
                fit
                :data="notifications">
                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop">
                    <template slot-scope="scope">
                        <template v-if="column.prop === 'type'">
                            <el-tag
                                :type="getType(scope.row.data.type)"
                                effect="dark">
                                {{ scope.row.data.type | ucWords }}
                            </el-tag>
                        </template>
                        <template v-else-if="column.prop === 'from'">
                            {{ scope.row.data.from | ucWords }}
                            <span v-if="scope.row.data.from === 'cron'">
                                : {{ scope.row.data.cron }}
                            </span>
                        </template>
                        <template v-else-if="column.prop === 'created_at'">
                            {{ scope.row.created_at | fixDateTimeByFormat }}
                        </template>
                    </template>
                </el-table-column>

                <el-table-column
                    label="Actions"
                    width="200">
                    <template slot-scope="scope">
                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="View Notification"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                @click="viewNotification(scope.row)"
                                type="text"
                                class="ml-2 text-secondary">
                                <i class="fas fa-eye"></i>
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Delete Notification"
                            :open-delay="500"
                            placement="top">
                            <el-popconfirm
                                @confirm="deleteNotification(scope.row)"
                                confirm-button-text='OK'
                                cancel-button-text='No, Thanks'
                                icon="el-icon-info"
                                icon-color="red"
                                title="Are you sure to delete this notification?">
                                <el-button
                                    type="text"
                                    class="text-danger ml-2"
                                    slot="reference">
                                    <i class="fas fa-trash-alt"></i>
                                </el-button>
                            </el-popconfirm>
                        </el-tooltip>
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
        name: "NotificationList",
        mixins: [pagination],
        data() {
            let columns = [
                {label: 'Type', prop: 'type', showOverflowTooltip: true, sortable: true},
                {label: 'From', prop: 'from', showOverflowTooltip: true, sortable: true},
                {label: 'Recorded At', prop: 'created_at', showOverflowTooltip: true, sortable: true},
            ]

            return {
                loading: false,
                filters: {
                    searchString: null
                },
                columns: columns,
                notifications: []
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

                let params = {...this.filters, ...this.pagination}

                this.$API.Notification.getList(params)
                    .then(res => {
                        if (res.data) {
                            this.notifications = res.data.data
                            this.pagination.total = res.data.total
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            viewNotification(notification) {
                this.$router.push({name: 'Notification View', params: {id: notification.id}})
            },
            deleteNotification(notification) {
                this.loading = true

                this.$API.Notification.delete(notification.id)
                    .then(res => {
                        if (res.data) {
                            this.getList()
                        }
                    })
                    .catch(err => {
                        console.log(err)
                        this.loading = false
                    })
            },
            getType(type) {
                if (type === 'error') {
                    return 'danger'
                }

                return 'info'
            }
        }
    }
</script>
