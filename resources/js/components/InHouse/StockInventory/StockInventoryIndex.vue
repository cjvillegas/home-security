<template>
    <div class="stock-inventory">
        <global-page-header title="Stock Inventory"></global-page-header>

        <div class="mt-3">
            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <el-card class="summary-container new-orders-container">
                        <div class="summary-title">New Orders</div>

                        <el-button
                            @click="addNewOrder"
                            class="mt-3">
                            Add New
                        </el-button>

                        <div class="mt-3 d-flex">
                            <div class="summary-analytic">OOS Items:</div>
                            <div class="ml-auto summary-analytic-count">35</div>
                        </div>
                    </el-card>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <el-card class="summary-container pending-orders-container">
                        <div class="summary-title">Pending Orders</div>

                        <el-button
                            class="mt-3">
                            Approve Orders
                        </el-button>

                        <div class="mt-3 d-flex">
                            <div class="summary-analytic">Pending Approval:</div>
                            <div class="ml-auto summary-analytic-count">35</div>
                        </div>
                    </el-card>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <el-card class="summary-container completed-orders-container">
                        <div class="summary-title">Completed Orders</div>

                        <el-button
                            class="mt-3">
                            View Orders
                        </el-button>

                        <div class="mt-3 d-flex">
                            <div class="summary-analytic">Approved Orders:</div>
                            <div class="ml-auto summary-analytic-count">35</div>
                        </div>
                    </el-card>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <el-card class="summary-table-container">
                <div class="mb-3 summary-table-title">Items arriving at the warehouse today.</div>

                <el-table
                    fit
                    :data="[]"
                    max-height="80vh">
                    <template
                        slot="empty">
                        <el-empty
                            description="No Records Found.">
                        </el-empty>
                    </template>

                    <el-table-column
                        v-for="col in columns"
                        :key="col.prop"
                        :label="col.label"
                        :show-overflow-tooltip="col.showOverflowTooltip"
                        :sortable="col.sortable">
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
                {label: 'Item Code', prop: 'item_code', showOverflowTooltip: true, sortable: true},
                {label: 'Description', prop: 'description', showOverflowTooltip: true, sortable: true},
                {label: 'Category', prop: 'category', showOverflowTooltip: true, sortable: true},
                {label: 'In Stock Now', prop: 'in_stock_now', showOverflowTooltip: true, sortable: true},
                {label: 'Arriving Stock', prop: 'arriving_stock', showOverflowTooltip: true, sortable: true},
            ]

            return {
                columns: columns
            }
        },

        methods: {
            addNewOrder() {
                this.$router.push({name: 'Stock Inventory Form'})
            }
        }
    }
</script>
