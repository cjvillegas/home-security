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

                <h4 class="ml-3 mb-0">Stock Inventory - Draft Orders</h4>
            </div>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div>
                <h5>Draft Items: <span>{{ totalCount }}</span></h5>
            </div>

            <el-table
                fit
                :data="draftOrders">
                <el-table-column
                    v-for="col in columns"
                    :key="col.prop"
                    :prop="col.prop"
                    :label="col.label"
                    :sortable="col.sortable">
                    <template slot-scope="scope">
                        <template v-if="col.prop === 'created_at'">
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
                        <el-button
                            @click="viewOrder(scope.row)"
                            type="primary"
                            size="mini">
                            View Order
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
    export default {
        name: "StockInventoryDraftOrders",

        data() {
            const columns = [
                {label: 'Order Number', prop: 'order_no', sortable: true},
                {label: 'Order Date', prop: 'created_at', sortable: true},
                {label: 'Item(s) On Order', prop: 'order_item_count', sortable: true},
                {label: 'Created By', prop: 'creator', sortable: true}
            ]

            return {
                loading: false,
                totalCount: 0,
                columns: columns,
                draftOrders: []
            }
        },

        created() {
            this.fetchDraftTotalCount()
            this.fetchDraftOrders()
        },

        methods: {
            fetchDraftOrders() {
                this.loading = true

                this.$API.StockOrder.fetchDraftOrders()
                .then(res => {
                    if (res.data) {
                        this.draftOrders = res.data
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            fetchDraftTotalCount() {
                this.$API.StockOrder.fetchDraftTotalCount()
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

            backToList() {
                this.$router.push({name: 'Stock Inventory Index'})
            }
        }
    }
</script>
