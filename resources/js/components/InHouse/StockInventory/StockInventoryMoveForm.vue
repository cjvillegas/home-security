<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Move Item Wizard"
        :before-close="closeForm"
        width="30%">
        <el-form
            v-loading="loading"
            :model="moveForm"
            :rules="rules"
            ref="moveForm">
            <el-form-item
                label="Select Order"
                prop="order_id">
                <el-select
                    v-model="moveForm.order_id"
                    clearable
                    filterable
                    placeholder="Please select an order"
                    class="w-100">
                    <el-option
                        v-for="order in pendingOrders"
                        :key="order.id"
                        :label="order.order_no"
                        :value="order.id">
                    </el-option>
                </el-select>
            </el-form-item>
        </el-form>

        <span
            slot="footer"
            class="dialog-footer">
		    <el-button
                @click="closeForm">
		    	Close
		    </el-button>
		    <el-button
                @click="validate"
                type="primary"
                class="btn-primary">
		    	Move Items
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
    import {dialog} from "../../../mixins/dialog"
    import {formHelper} from '../../../mixins/formHelper'
    import cloneDeep from "lodash/cloneDeep";

    export default {
        name: "StockInventoryMoveForm",

        mixins: [dialog, formHelper],

        props: {
            orderLines: {
                required: true,
                type: Array
            },

            currentOrder: {}
        },

        data() {
            return {
                loading: false,
                moveForm: {
                    order_id: null
                },
                rules: {
                    order_id: {required: true, message: 'Please select an order.', trigger: 'change'}
                },
                pendingOrders: []
            }
        },

        methods: {
            validate() {
                this.$refs.moveForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()

                        this.moveItems()
                    }
                })
            },

            moveItems() {
                this.loading = true

                let postData = cloneDeep(this.moveForm)
                postData.order_item_ids = this.orderLines.map(ol => ol.id).filter(ol => !!ol)

                this.$API.StockOrder.moveItems(postData)
                .then(res => {
                    if (res.data.data) {
                        this.$EventBus.fire('STOCK_ORDERING_ITEMS_MOVED', res.data.data)

                        this.closeForm()

                        this.$notify({
                            title: 'Stock Ordering',
                            message: res.data.message,
                            type: 'success'
                        })
                    }
                })
                .catch(err => {
                    console.error(err)

                    this.$notify({
                        title: 'Stock Ordering',
                        message: "Failed to move items.",
                        type: 'error'
                    })
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            fetchPendingOrders() {
                this.loading = true

                this.$API.StockOrder.fetchPendingOrders()
                    .then(res => {
                        if (res.data) {
                            this.pendingOrders = res.data

                            let index = res.data.findIndex(po => po.id === this.currentOrder.id)

                            if (index > -1) {
                                this.pendingOrders.splice(index, 1)
                            }
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            closeForm() {
                this.resetForm()

                setTimeout(_ => {
                    if (this.$refs.moveForm) {
                        this.$refs.moveForm.clearValidate()
                    }

                    this.closeModal()
                }, 300)
            },

            resetForm() {
                this.moveForm = {
                    order_id: null
                }
            }
        },

        watch: {
            showDialog() {
                if (this.showDialog) {
                    this.fetchPendingOrders()
                }
            }
        }
    }
</script>
