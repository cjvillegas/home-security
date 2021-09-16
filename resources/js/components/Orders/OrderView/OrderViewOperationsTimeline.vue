<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Operations Timeline"
        :before-close="closeDialog"
        append-to-body
        width="80%"
        top="10vh">
        <order-progress
            :order="order"
            :orders="orders"
            :process-sequences="processSequences">
        </order-progress>
    </el-dialog>
</template>

<script>
    import { dialog } from '../../../mixins/dialog'

    export default {
        name: "OrderViewOperationsTimeline",

        mixins: [dialog],

        props: {
            order: {
                required: true
            },
            processSequences: {},
            orders: {},
        },

        methods: {
            closeDialog() {
                this.closeModal()
            }
        },
    }
</script>
