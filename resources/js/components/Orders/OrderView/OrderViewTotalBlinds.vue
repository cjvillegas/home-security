<template>
    <div>
        <el-dialog
            :visible.sync="showDialog"
            title="Total Blinds"
            :before-close="closeModal"
            width="70%"
            top="10vh">
            <el-table
                :data="orders"
                fit
                v-loading="loading">
                <el-table-column
                    prop="id"
                    label="ID">
                </el-table-column>
                <el-table-column
                    prop="order_no"
                    label="Order No">
                </el-table-column>
                <el-table-column
                    prop="customer"
                    label="Customer">
                </el-table-column>
                <el-table-column
                    prop="customer_order_no"
                    label="Customer Order No.">
                </el-table-column>
                <el-table-column
                    prop="quantity"
                    label="Quantity">
                </el-table-column>
                <el-table-column
                    prop="blind_type"
                    label="Blind Type">
                </el-table-column>
                <el-table-column
                    label="Product Type">
                    <template slot-scope="scope">
                        <template>
                            {{ scope.row.product_type }}
                            <a href="#" @click="openUpdateFormDialog(scope.row)">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </template>
                    </template>
                </el-table-column>
            </el-table>
        </el-dialog>

        <order-update-product-type-form
            :order="selectedOrder"
            :visible.sync="showUpdateForm"
            @close="showUpdateForm = false">
        </order-update-product-type-form>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import { dialog } from '../../../mixins/dialog'

    export default {
        name: "OrderViewTotalBlinds",

        mixins: [dialog],

        props: {
            order_no: {
                required: true,
            },
        },

        data() {
            return {
                loading: false,
                selectedOrder: {},
                showUpdateForm: false,
                orders: [],
            }
        },
        created() {
            this.functionName = 'getOrdersByOderNo'

            this.$EventBus.listen('PRODUCT_TYPE_UPDATE', _ => {
                this.getOrdersByOrderNo()
            })
        },
        methods: {
            openUpdateFormDialog(row) {
                this.selectedOrder = cloneDeep(row)
                this.showUpdateForm = true
            },

            getOrdersByOrderNo() {
                this.loading = true
                console.log(this.order_no)

                this.$API.Orders.getOrdersByOrderNo(this.order_no)
                .then(res => {
                    this.orders = res.data
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },
        },

        watch: {
            order_no: {
                handler() {
                    this.getOrdersByOrderNo()
                }
            }
        }
    }
</script>
