<template>
    <div>
        <el-card class="card">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search Stock Level name or code..."
                        @keyup.enter.native.prevent="fetchStockLevels"
                        style="width: 250px">
                    </el-input>
                </div>
                <div class="ml-auto">
                    <span class="text-muted">
                        Last Sync: {{ lastSync }}
                    </span>
                </div>
            </div>
            <div v-loading="loading">
                <el-table
                    :data="stockLevels">
                    <el-table-column
                        prop="code"
                        label="Code"
                        width="200px"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="name"
                        label="Description"
                        width="1000"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="available_stock"
                        label="Available Stock"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        label="Incoming Stock"
                        class-name="table-action-button">
                        <template slot-scope="scope">
                            <template>
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="View Purchase Orders"
                                    placement="top">
                                    <el-button
                                        @click="viewPurchaseOrder(scope.row.purchase_orders)"
                                        class="btn-default text-default">
                                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                    </el-button>
                                </el-tooltip>
                            </template>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <el-pagination
                class="custom-pagination-class  mt-3 float-right"
                background
                layout="total, sizes, prev, pager, next"
                :total="filters.total"
                :page-size="filters.size"
                :page-sizes="[1, 2, 10, 25, 50, 100]"
                :current-page="filters.page"
                @size-change="handleSize"
                @current-change="handlePage">
            </el-pagination>
        </el-card>

        <purchase-order-view
            :purchaseOrders="purchaseOrders"
            :visible.sync="showPurchaseOrderView"
            @close="closeForm">

        </purchase-order-view>
    </div>
</template>

<script>
    import pagination from '../../mixins/pagination'
    export default {
        mixins: [pagination],
        props: {
            user: {
                required: true,
                type: Object
            }
        },

        data() {
            return {
                loading: false,
                stockLevels: [],
                filters: {
                    searchString: ''
                },
                selected_id: '',
                viewDialogVisible: false,
                lastSync: '',
                showPurchaseOrderView: false,
                purchaseOrders: [],
            }
        },

        mounted() {
            this.filters.size = 10
            this.functionName = 'fetchStockLevels'
            this.fetchStockLevels()
            this.fetchLastSync()
        },

        methods: {
            fetchStockLevels() {
                let apiUrl = `/admin/in-house/stock-levels/list`
                this.loading = true
                axios.post(apiUrl, this.filters)
                .then((response) => {
                    this.stockLevels = response.data.stockLevels.data
                    this.filters.total = response.data.stockLevels.total
                    console.log(this.stockLevels)
                })
                .catch( (err) => {
                    console.log(err)
                })
                .finally( () => {
                    this.loading = false
                })
            },

            fetchLastSync() {
                let apiUrl = `/admin/in-house/stock-levels/last-sync`
                axios.get(apiUrl)
                .then((response) => {
                    console.log(response.data)
                    this.lastSync = moment(response.data.lastSync.created_at).format('MMMM Do YYYY, h:mm:ss a')
                })
            },

            viewStockLevel(id) {
                this.selected_id = id
                this.viewDialogVisible = true
            },

            viewPurchaseOrder(data) {
                this.purchaseOrders = data
                this.showPurchaseOrderView = true
            },

            closeForm() {
                this.viewDialogVisible = false
                this.showPurchaseOrderView = false
            }
        }
    }
</script>
