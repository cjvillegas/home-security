<template>
    <div>
        <el-card class="box-card">
            <div class="d-flex align-items-center">
                <el-button
                    @click="backToList">
                    <i class="fas fa-arrow-left"></i> Back to List
                </el-button>
            </div>
        </el-card>

        <el-card class="box-card mt-3">
            <div class="d-flex mb-3">
                <div>Order No: 100010INT</div>
                <div class="ml-3">Date: Oct 22, 2021</div>
                <div class="ml-auto">Status: Draft</div>
            </div>

            <el-form>
                <el-table
                    fit
                    border
                    :data="stockOrder">
                    <el-table-column
                        v-for="col in columns"
                        :key="col.prop"
                        :label="col.label"
                        :prop="col.prop"
                        :sortable="col.sortable">
                        <template slot-scope="scope">
                            <template v-if="col.prop === 'product'">
                                <el-autocomplete
                                    @select="selectItemFromSearch"
                                    :fetch-suggestions="searchStockLevels"
                                    @focus="setFocusedOrderLine(`orderCode_${scope.$index}`)"
                                    @keyup.enter.native.prevent="handleEnterCodeSelection(`orderCode_${index}`)"
                                    :trigger-on-focus="false"
                                    v-model="scope.row.product"
                                    value-key="code"
                                    :debounce="0"
                                    placeholder="Search Product..."
                                    :select-when-unmatched="true"
                                    :highlight-first-item="true"
                                    :ref="`orderCode_${scope.$index}`"
                                    class="w-100">
                                    <template slot-scope="{item}">
                                        <div class="sbg-select-item">
                                            <div class="item-title">{{ item.code }}</div>
                                            <small class="item-description">{{ item.name }}</small>
                                        </div>
                                    </template>
                                </el-autocomplete>
                            </template>
                            <template v-else-if="col.prop === 'order_qty'">
                                <el-input-number
                                    @focus="setFocusedOrderLine(`qty_${scope.$index}`)"
                                    @blur="handleQtyBlur"
                                    v-model="scope.row.order_qty"
                                    class="w-100"
                                    type="number"
                                    :min="0"
                                    :ref="`qty_${scope.$index}`">
                                </el-input-number>
                            </template>
                            <template v-else>
                                {{ scope.row[col.prop] }}
                            </template>
                        </template>
                    </el-table-column>

                    <el-table-column
                        label="Action"
                        width="60">
                        <template slot-scope="scope">
                            <el-popconfirm
                                @confirm="removeOrderLine(scope.$index)"
                                confirm-button-text='OK'
                                cancel-button-text='No, Thanks'
                                icon="el-icon-info"
                                icon-color="red"
                                title="Are you sure to delete this order line?">
                                <el-button
                                    slot="reference"
                                    type="text">
                                    <i class="fas fa-trash-alt text-danger"></i>
                                </el-button>
                            </el-popconfirm>
                        </template>
                    </el-table-column>
                </el-table>
            </el-form>

            <div class="d-flex mt-5">
                <el-button
                    @click="addNewOrderLine"
                    type="text"
                    class="text-black-50 f-size-2em font-weight-bolder">
                    <i class="fas fa-cart-plus"></i> Add a Product
                </el-button>

                <div class="ml-auto">
                    <el-button
                        type="danger"
                        size="mini">
                        Cancel
                    </el-button>

                    <el-button
                        type="success"
                        size="mini">
                        Save
                    </el-button>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
    import axios from 'axios'
    import cloneDeep from 'lodash/cloneDeep'

    export default {
        name: "StockInventoryForm",

        data() {
            const columns = [
                {label: 'Product', prop: 'product', sortable: true},
                {label: 'Description', prop: 'name', sortable: false},
                {label: 'Qty Needed', prop: 'order_qty', sortable: true},
                {label: 'Qty In Stock WH', prop: 'qty_in_stock', sortable: true},
                {label: 'Pending Orders', prop: 'pending_orders_count', sortable: true},
            ]

            return {
                loading: false,
                focusedField: null,
                columns: columns,
                stockOrder: [],
            }
        },

        created() {
            this.stockOrder.push(this.generateEmptyObject())
        },

        methods: {
            /**
             * API requests
             */
            generateNewOrder() {
                this.loading = true

                this.$API.StockOrder
            },

            searchStockLevels(searchString, cb) {
                let selectedIds = this.stockOrder.map(line => line.id).filter(id => !!id)
                let params = {
                    searchString,
                    ids: selectedIds
                }

                axios.get(`/admin/in-house/stock-inventories/search-stock-levels`, {
                    params
                })
                .then(res => {
                    cb(res.data)
                })
                .catch(err => {
                    console.log(err)
                })
            },

            addNewOrderLine() {
                this.stockOrder.push(this.generateEmptyObject())
            },

            removeOrderLine(index) {
                this.stockOrder.splice(index, 1)
            },

            generateEmptyObject() {
                return {
                    id: null,
                    code: null,
                    name: "Gusto siya nga naay description",
                    order_qty: 0,
                    qty_in_stock: 0,
                    pending_orders: 0
                }
            },

            /**
             * Methods defined after this block are used in form behavior
             */
            setFocusedOrderLine(ref) {
                this.focusedField = cloneDeep(ref)
            },

            setNextFocusedField(index, fieldName) {
                // if the next index is the last index, then generate first a new order line
                if (fieldName === 'qty' && (index + 1) > this.stockOrder.length) {
                    this.stockOrder.push(this.generateEmptyObject())
                }

                this.focusedField = fieldName === 'orderCode' ? `qty_${index}` : `orderCode_${index}`

                setTimeout(() => {
                    if (this.$refs[this.focusedField]) {
                        this.$refs[this.focusedField][0].focus()
                    }
                }, 100)
            },

            getFocusedFieldSections() {
                let index = null
                let name = null

                if (this.focusedField) {
                    let exploded = this.focusedField.split('_')
                    name = exploded[0]
                    index = Number(exploded[1])
                }

                return {
                    index,
                    name
                }
            },

            selectItemFromSearch(item) {
                let focusedField = this.getFocusedFieldSections()
                let index = focusedField.index
                let name = focusedField.name

                // sanity check: a focused item should be present before populating the order line with
                // the selected order item
                if (index === null) {
                    console.error('Error: No focused field found in the order form.')

                    return
                }

                let orderLine = cloneDeep(this.stockOrder[index])

                // sanity check: an order line should be present for population
                if (!orderLine) {
                    console.error('Error: No order line found by the given order line index.')

                    return
                }

                orderLine.id = item.id
                orderLine.code = item.code
                orderLine.name = item.name
                orderLine.qty_in_stock = item.available_stock

                let nextIndex = name === 'orderCode' ? index : index + 1
                this.setNextFocusedField(nextIndex, name)

                this.stockOrder.splice(index, 1, orderLine)
            },

            handleEnterCodeSelection(ref) {
                if (this.$refs[ref]) {
                    this.$refs[ref].activated = false
                }
            },

            handleQtyBlur() {
                let focusedField = this.getFocusedFieldSections()
                let index = focusedField.index
                let name = focusedField.name

                let nextIndex = name === 'orderCode' ? index : index + 1
                this.setNextFocusedField(nextIndex, name)
            },

            /**
             * Page navigation methods
             */
            backToList() {
                this.$router.push({name: 'Stock Inventory Index'})
            },
        }
    }
</script>
