<template>
    <div class="stock-inventory">
        <el-card
            v-loading="loading"
            class="box-card">
            <div class="d-flex align-items-center">
                <el-button
                    @click="backToList">
                    <i class="fas fa-arrow-left"></i> Back to List
                </el-button>

                <h4 class="ml-3 mb-0">Stock Inventory - Approved Orders</h4>
            </div>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div>
                <h5>Approved Orders: <span>{{ totalCount }}</span></h5>
            </div>

            <el-table
                fit
                :data="approvedOrders">
                <el-table-column
                    v-for="col in columns"
                    :key="col.prop"
                    :prop="col.prop"
                    :label="col.label"
                    :sortable="col.sortable">
                    <template slot-scope="scope">
                        <template v-if="['created_at', 'approved_at'].includes(col.prop)">
                            {{ scope.row.created_at | fixDateByFormat }}
                        </template>

                        <template v-else-if="col.prop === 'creator'">
                            {{ scope.row.creator | ucWords }}
                        </template>

                        <template v-else>
                            <span>{{ scope.row[col.prop] }}</span>
                        </template>
                    </template>
                </el-table-column>

                <el-table-column>
                    <template slot-scope="scope">
                        <div>
                            <el-button
                                @click="viewOrder(scope.row)"
                                type="primary"
                                size="mini">
                                View Order
                            </el-button>
                        </div>

                        <div class="mt-2">
                            <el-button
                                @click="copyOrder(scope.row)"
                                size="mini"
                                style="background: #f9c710">
                                Copy Order
                            </el-button>
                        </div>

                        <div
                            v-if="scope.row.picking_url"
                            class="mt-2">
                            <el-button
                                @click="viewPickingList(scope.row)"
                                size="mini">
                                View Picking List
                            </el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
    export default {
        name: "StockInventoryApproveOrders",

        data() {
            const columns = [
                {label: 'Order Number', prop: 'order_no', sortable: true},
                {label: 'Order Date', prop: 'created_at', sortable: true},
                {label: 'Item(s) On Order', prop: 'order_item_count', sortable: true},
                {label: 'Created By', prop: 'creator', sortable: true},
                {label: 'Approved By', prop: 'approver', sortable: true},
                {label: 'Approved At', prop: 'approved_at', sortable: true},
                {label: 'Sage Order Number', prop: 'sage_order_no', sortable: true}
            ]

            return {
                loading: false,
                totalCount: 0,
                columns: columns,
                approvedOrders: []
            }
        },

        created() {
            this.fetchApprovedTotalCount()
            this.fetchPendingOrders()
        },

        methods: {
            copyOrder(order) {
                this.$confirm('Are you sure you want to clone this order?', 'Confirmation', {
                    confirmButtonText: 'Yes, I\'m sure!',
                    cancelButtonText: 'Nope, cancel!',
                    type: 'success'
                })
                .then(_ => {
                    this.loading = true

                    this.$API.StockOrder.cloneOrder(order.id)
                        .then(res => {

                        })
                        .catch(err => {
                            console.log(err)
                        })
                        .finally(_ => {
                            this.loading = false
                        })
                })
                .catch(_ => {})
            },

            fetchPendingOrders() {
                this.loading = true

                this.$API.StockOrder.fetchApprovedOrders()
                    .then(res => {
                        if (res.data) {
                            this.approvedOrders = res.data
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            fetchApprovedTotalCount() {
                this.$API.StockOrder.fetchApprovedTotalCount()
                .then(res => {
                    if (res.data) {
                        this.totalCount = res.data
                    }
                })
                .catch(err => {
                    console.log(err)
                })
            },

            viewOrder(order) {
                this.$router.push({
                    name: 'Stock Inventory Form View',
                    params: {
                        isNew: false,
                        id: order.id
                    }
                })
            },

            viewPickingList(order) {
                window.open(order.picking_url)
            },

            backToList() {
                this.$router.push({name: 'Stock Inventory Index'})
            }
        }
    }
</script>
