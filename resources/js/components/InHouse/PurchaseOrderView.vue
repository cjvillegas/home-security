<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Purchase Orders"
        @close="closeForm"
        width="60%">
        <el-table
            :data="purchaseOrders">
            <el-table-column
                prop="code"
                label="Stock Item">
            </el-table-column>
            <el-table-column
                prop="order_date"
                label="Order Date">
            </el-table-column>
            <el-table-column
                prop="estimated_delivery"
                label="Estimated Delivery Date">
            </el-table-column>
            <el-table-column
                prop="quantity"
                label="Quantity to Receive"
                sortable>
            </el-table-column>
        </el-table>
    </el-dialog>
</template>

<script>
    import {dialog} from "../../mixins/dialog";
    export default {
        name: "PurchaseOrderView",
        mixins: [dialog],
        props: {
            purchaseOrders: {
                required: true,
                type: Array
            }
        },
        data() {
            return {

            }
        },
        methods: {
            closeForm() {
                this.closeModal()
            }
        }
    }
</script>
