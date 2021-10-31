<template>
    <div class="stock-inventory">
        <global-page-header title="Stock Inventory"></global-page-header>

        <div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 mt-3">
                    <el-card class="summary-container new-orders-container">
                        <div class="summary-title">New Orders</div>

                        <el-button
                            @click="addNewOrder"
                            plain
                            type="primary"
                            class="mt-3">
                            Add New
                        </el-button>

                        <div class="mt-3 d-flex">
                            <div class="summary-analytic">OOS Items:</div>
                            <div class="ml-auto summary-analytic-count">
                                <el-skeleton
                                    class="text-right"
                                    :count="1"
                                    :loading="loaders.pendingCountLoader"
                                    animated>
                                    <template slot="template">
                                        <el-skeleton-item
                                            class="w-100"
                                            variant="text">
                                        </el-skeleton-item>
                                    </template>
                                    <template>
                                        <span>{{ totalOutOfStockItems | numFormat }}</span>
                                    </template>
                                </el-skeleton>
                            </div>
                        </div>
                    </el-card>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 mt-3">
                    <el-card class="summary-container draft-orders-container">
                        <div class="summary-title">Draft Orders</div>

                        <el-button
                            @click="draftOrders"
                            plain
                            type="primary"
                            class="mt-3">
                            View Drafts
                        </el-button>

                        <div class="mt-3 d-flex">
                            <div class="summary-analytic">Draft Items:</div>
                            <div class="ml-auto summary-analytic-count">
                                <el-skeleton
                                    class="text-right"
                                    :count="1"
                                    :loading="loaders.pendingCountLoader"
                                    animated>
                                    <template slot="template">
                                        <el-skeleton-item
                                            class="w-100"
                                            variant="text">
                                        </el-skeleton-item>
                                    </template>
                                    <template>
                                        <span>{{ totalDraftCount | numFormat }}</span>
                                    </template>
                                </el-skeleton>
                            </div>
                        </div>
                    </el-card>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 mt-3">
                    <el-card class="summary-container pending-orders-container">
                        <div class="summary-title">Pending Orders</div>

                        <el-button
                            @click="pendingOrders"
                            plain
                            type="primary"
                            class="mt-3">
                            Approve Orders
                        </el-button>

                        <div class="mt-3 d-flex">
                            <div class="summary-analytic">Pending Items:</div>
                            <div class="ml-auto summary-analytic-count">
                                <el-skeleton
                                    class="text-right"
                                    :count="1"
                                    :loading="loaders.pendingCountLoader"
                                    animated>
                                    <template slot="template">
                                        <el-skeleton-item
                                            class="w-100"
                                            variant="text">
                                        </el-skeleton-item>
                                    </template>
                                    <template>
                                        <span>{{ totalPendingCount | numFormat }}</span>
                                    </template>
                                </el-skeleton>
                            </div>
                        </div>
                    </el-card>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 mt-3">
                    <el-card class="summary-container completed-orders-container">
                        <div class="summary-title">Completed Orders</div>

                        <el-button
                            @click="approvedOrders"
                            plain
                            type="primary"
                            class="mt-3">
                            View Orders
                        </el-button>

                        <div class="mt-3 d-flex">
                            <div class="summary-analytic">Approved Orders:</div>
                            <div class="ml-auto summary-analytic-count">
                                <el-skeleton
                                    class="text-right"
                                    :count="1"
                                    :loading="loaders.pendingCountLoader"
                                    animated>
                                    <template slot="template">
                                        <el-skeleton-item
                                            class="w-100"
                                            variant="text">
                                        </el-skeleton-item>
                                    </template>
                                    <template>
                                        <span>{{ totalApprovedCount | numFormat }}</span>
                                    </template>
                                </el-skeleton>
                            </div>
                        </div>
                    </el-card>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <el-card class="summary-table-container">
                <div class="mb-3 summary-table-title">Items expected to arrive at the warehouse today.</div>

                <el-table
                    v-loading="loaders.purchaseOrder"
                    fit
                    :data="purchaseOrders"
                    max-height="50vh"
                    min-height="20vh"
                    style="overflow-y: auto">
                    <template
                        slot="empty">
                        <el-empty
                            description="No Records Found.">
                        </el-empty>
                    </template>

                    <el-table-column
                        v-for="col in columns"
                        :key="col.prop"
                        :prop="col.prop"
                        :label="col.label"
                        :show-overflow-tooltip="col.showOverflowTooltip"
                        :sortable="col.sortable">
                        <template slot-scope="scope">
                            <template v-if="['in_stock_now', 'arriving_stock'].includes(col.prop)">
                                {{ scope.row[col.prop] | numFormat }}
                            </template>

                            <template v-else>
                                {{ scope.row[col.prop] }}
                            </template>
                        </template>
                    </el-table-column>
                </el-table>
            </el-card>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StockInventoryIndex",

        data() {
            const columns = [
                {label: 'Item Code', prop: 'code', showOverflowTooltip: true, sortable: true},
                {label: 'Description', prop: 'description', showOverflowTooltip: true, sortable: true},
                {label: 'Category', prop: 'product_category', showOverflowTooltip: true, sortable: true},
                {label: 'In Stock Now', prop: 'in_stock_now', showOverflowTooltip: true, sortable: true},
                {label: 'Arriving Stock', prop: 'arriving_stock', showOverflowTooltip: true, sortable: true},
            ]

            return {
                columns: columns,
                loaders: {
                    draftCountLoader: false,
                    outOfStockLoader: false,
                    pendingCountLoader: false,
                    approvedCountLoader: false,
                    purchaseOrder: false
                },
                totalOutOfStockItems: 0,
                totalDraftCount: 0,
                totalPendingCount: 0,
                totalApprovedCount: 0,
                purchaseOrders: []
            }
        },

        created() {
            this.fetchOutOfStockItemsTotalCount()
            this.fetchPendingTotalCount()
            this.fetchApprovedTotalCount()
            this.fetchPurchaseOrders()
            this.fetchDraftTotalCount()
        },

        methods: {
            fetchOutOfStockItemsTotalCount() {
                this.loaders.outOfStockLoader = true

                this.$API.StockLevel.fetchOutOfStockItemsTotalCount()
                    .then(res => {
                        if (res.data) {
                            this.totalOutOfStockItems = res.data.data
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loaders.outOfStockLoader = false
                    })
            },

            fetchDraftTotalCount() {
                this.loaders.draftCountLoader = true

                this.$API.StockOrder.fetchDraftTotalCount()
                .then(res => {
                    if (res.data) {
                        this.totalDraftCount = res.data
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loaders.draftCountLoader = false
                })
            },

            fetchPendingTotalCount() {
                this.loaders.pendingCountLoader = true

                this.$API.StockOrder.fetchPendingTotalCount()
                .then(res => {
                    if (res.data) {
                        this.totalPendingCount = res.data
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loaders.pendingCountLoader = false
                })
            },

            fetchApprovedTotalCount() {
                this.loaders.approvedCountLoader = true

                this.$API.StockOrder.fetchApprovedTotalCount()
                    .then(res => {
                        if (res.data) {
                            this.totalApprovedCount = res.data
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loaders.approvedCountLoader = false
                    })
            },

            fetchPurchaseOrders() {
                this.loaders.purchaseOrder = true

                let params = {
                    start: moment().startOf('day').format('YYYY-MM-DD HH:mm:ss'),
                    end: moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'),
                }

                this.$API.PurchaseOrder.fetchPurchaseOrders(params)
                .then(res => {
                    if (res.data) {
                        this.purchaseOrders = res.data
                    }
                })
                .catch(err => {
                    console.error(err)
                })
                .finally(_ => {
                    this.loaders.purchaseOrder = false
                })
            },

            addNewOrder() {
                this.$router.push({
                    name: 'Stock Inventory Form',
                    params: {
                        isNew: true
                    }
                })
            },

            draftOrders() {
                this.$router.push({
                    name: 'Stock Inventory Draft Orders',
                    params: {}
                })
            },

            pendingOrders() {
                this.$router.push({
                    name: 'Stock Inventory Pending Orders',
                    params: {}
                })
            },

            approvedOrders() {
                this.$router.push({
                    name: 'Stock Inventory Approved Orders',
                    params: {}
                })
            }
        }
    }
</script>
