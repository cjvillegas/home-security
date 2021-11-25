<template>
    <div>
        <div class="float-right">
            <ul>
                <li>Order No. : {{ selectedOrderNo }}</li>
                <li>Customer: {{ orders[0].customer }}</li>
                <li>Blind(s): {{ orders.length }}</li>
            </ul>
        </div>
        <el-table
            fit
            ref="multipleTable"
            :data="orders"
            @selection-change="handleSelectionChange"
            v-loading="loading">
            <template
                slot="empty">
                <el-empty
                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                </el-empty>
            </template>
            <el-table-column
                type="selection"
                width="55">
            </el-table-column>
            <el-table-column
                prop="serial_id"
                label="Blind ID"
                sortable>
            </el-table-column>

            <el-table-column
                prop="width"
                label="Width"
                sortable>
            </el-table-column>

            <el-table-column
                prop="drop"
                label="Drop"
                sortable>
            </el-table-column>

            <el-table-column
                prop="stock_code"
                label="Fabric Code"
                sortable>
            </el-table-column>

            <el-table-column
                prop="fabric_range"
                label="Fabric Range"
                sortable>
            </el-table-column>

            <el-table-column
                prop="color"
                label="Fabric Colour"
                sortable>
            </el-table-column>

            <el-table-column
                prop="product_type"
                label="Product Type"
                sortable>
            </el-table-column>
        </el-table>
        <div class="float-right mb-5 mt-3">
            <el-button-group>
                <el-button
                    type="danger"
                    class="mr-2"
                    icon="el-icon-arrow-left"
                    @click="cancelAction">
                    Back
                </el-button>
                <el-button
                    type="primary"
                    @click="confirmDialog">
                    Continue
                    <i class="el-icon-arrow-right el-icon-right"></i>
                </el-button>
            </el-button-group>
        </div>

        <selected-blinds-dialog
            :visible.sync="showBlindsDialog"
            @close="closeDialog">
        </selected-blinds-dialog>
    </div>
</template>

<script>
    import { mapActions, mapGetters, mapMutations } from 'vuex'
    export default {
        name: "RemakeOrders",
        data() {
            return {
                showBlindsDialog: false
            }
        },
        computed: {
            ...mapGetters('remakeChecker', ['selectedOrderNo', 'selectedBlindId', 'orders', 'loading']),
        },
        methods: {
            ...mapActions('remakechecker', ['backToMainScreen']),
            ...mapMutations('remakechecker', ['setSelectedBlindId']),
            handleSelectionChange(val) {
                this.setSelectedBlindId(val)
            },
            confirmDialog() {
                if (this.selectedOrderNo.length == 0) {
                    this.$notify({
                        title: 'Invalid input',
                        message: 'Must select atleast one Blind ID',
                        type: 'error'
                    })
                    return
                }
                this.showBlindsDialog = true
            },
            cancelAction() {
                this.backToMainScreen()
                this.$notify({
                    title: 'Remake Checker',
                    message: 'Action Cancelled',
                    type: 'warning'
                })
            },
            closeDialog() {
                this.showBlindsDialog = false
            }
        }
    }
</script>
