<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        @close="closeForm"
        width="60%">
        <el-table
            v-if="hasTrackings"
            fit
            :data="orderTrackings">
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
                <template
                    slot-scope="scope">
                    {{ scope.row.created_at | fixDateTimeByFormat('MMMM DD, YYYY hh:mm:ss a') }}
                </template>
            </el-table-column>
        </el-table>

        <el-empty
            v-else
            description="No Order Tracking Yet. Wait for the next sync of the data from the BlindData database and recheck again.">
        </el-empty>
    </el-dialog>
</template>

<script>
    import { dialog } from "../../mixins/dialog";
    import { formHelper } from "../../mixins/formHelper";

    export default {
        name: "OrderTracking",

        mixins: [dialog, formHelper],

        props: {
            order: {
                required: true,
            },
            orderTrackings: []
        },

        data() {
            return {
                loading: false,
            }
        },

        methods: {
            closeForm() {
                this.closeModal()
            }
        },

        computed: {
            dialogTitle() {
                return 'Tracking Information for Order ' + (this.order ? this.order.order_no : '')
            },

            hasTrackings() {
                return this.orderTrackings && this.orderTrackings.length
            }
        }
    }
</script>
