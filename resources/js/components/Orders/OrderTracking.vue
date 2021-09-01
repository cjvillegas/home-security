<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        @close="closeForm"
        width="60%">
        <el-table
            :data="trackings">
            <el-table-column
                prop="order_no"
                label="Order No."
                sortable>
            </el-table-column>

            <el-table-column
                prop="cust_ref"
                label="Customer Reference"
                sortable>
            </el-table-column>

            <el-table-column
                prop="tracking_no"
                label="Tracking Number"
                sortable>
            </el-table-column>

            <el-table-column
                prop="courier"
                label="Courier"
                sortable>
            </el-table-column>

            <el-table-column
                prop="created_at"
                label="Shipped Date"
                sortable>
            </el-table-column>
        </el-table>
    </el-dialog>
</template>

<script>
    import {dialog} from "../../mixins/dialog";
    import {formHelper} from "../../mixins/formHelper";
    export default {
        name: "OrderTracking",
        mixins: [dialog, formHelper],
        props: {
            trackings: {
                required: true,
                type: Array
            }
        },

        methods: {
            closeForm() {
                this.closeModal()
            }
        },

        computed: {
            dialogTitle() {
                return 'Tracking Information for Order ' + this.trackings[0].order_no
            }
        },

        mounted() {
            console.log(this.trackings)
        }
    }
</script>
