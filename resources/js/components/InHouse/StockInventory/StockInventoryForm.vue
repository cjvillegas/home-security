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
            </div>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <el-descriptions
                v-if="order"
                class="margin-top"
                :column="3"
                size="small"
                border>
                <el-descriptions-item>
                    <template slot="label">
                        <i class="fas fa-box"></i>
                        Order No:
                    </template>
                    {{ order.order_no }}
                </el-descriptions-item>

                <el-descriptions-item>
                    <template slot="label">
                        <i class="far fa-calendar"></i>
                        Date:
                    </template>
                    {{ order.created_at | fixDateByFormat }}
                </el-descriptions-item>

                <el-descriptions-item>
                    <template slot="label">
                        <i class="far fa-file"></i>
                        Status:
                    </template>
                    <span :class="`status-${order.status}`">{{ order.status_name }}</span>
                </el-descriptions-item>
            </el-descriptions>

            <div
                v-if="isPending && isApprover"
                class="mt-3 text-right">
                <el-tooltip
                    class="item"
                    effect="dark"
                    content="No selected order item or no other pending orders."
                    :disabled="enableMoveButton"
                    placement="top">
                    <span>
                        <el-button
                            @click="moveOrderLines"
                            :disabled="!enableMoveButton"
                            type="info"
                            size="mini">
                            Move Order
                        </el-button>
                   </span>
                </el-tooltip>

                <el-tooltip
                    class="item"
                    effect="dark"
                    content="No selected order item."
                    :disabled="enableMoveButton"
                    placement="top">
                    <span>
                        <el-button
                            @click="moveToNewOrder"
                            :disabled="!enableMoveButton"
                            type="info"
                            style="background: #3e47a7"
                            size="mini">
                            Create New Order
                        </el-button>
                   </span>
                </el-tooltip>

                <el-button
                    v-if="isPending"
                    @click="cancelOrder"
                    type="danger"
                    size="mini">
                    Cancel Order
                </el-button>

                <el-button
                    v-if="isPending"
                    @click="approveOrder"
                    type="primary"
                    size="mini"
                    class="ml-0">
                    Approve Order
                </el-button>
            </div>

            <el-form class="mt-3">
                <el-table
                    fit
                    border
                    :data="stockOrder"
                    @selection-change="handleItemSelected">
                    <el-table-column
                        v-if="isPending && !isProcessed"
                        type="selection"
                        width="55">
                    </el-table-column>
                    <el-table-column
                        v-for="col in columns"
                        :key="col.prop"
                        :label="col.label"
                        :prop="col.prop"
                        :sortable="col.sortable">
                        <template slot-scope="scope">
                            <template v-if="col.prop === 'code'">
                                <el-autocomplete
                                    @select="handleSelectItem"
                                    :fetch-suggestions="searchStockLevels"
                                    @focus="setFocusedOrderLine(`orderCode_${scope.$index}`)"
                                    @keyup.enter.native.prevent="handleEnterCodeSelection(`orderCode_${index}`)"
                                    :disabled="isProcessed"
                                    :trigger-on-focus="false"
                                    v-model="scope.row.code"
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
                                <el-input
                                    @focus="setFocusedOrderLine(`qty_${scope.$index}`)"
                                    @change="handleQtyChange"
                                    v-model="scope.row.order_qty"
                                    :ref="`qty_${scope.$index}`"
                                    :disabled="!scope.row.id || isProcessed"
                                    class="w-100"
                                    type="number"
                                    :min="0"
                                    placeholder="Enter Quantity">
                                </el-input>
                            </template>
                            <template v-else>
                                {{ scope.row[col.prop] }}
                            </template>
                        </template>
                    </el-table-column>

                    <el-table-column
                        v-if="!isProcessed"
                        label="Action"
                        width="60">
                        <template slot-scope="scope">
                            <el-popconfirm
                                @confirm="removeOrderLine(scope.$index, scope.row)"
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
                    v-if="!isProcessed"
                    @click="addNewOrderLine"
                    type="text"
                    class="text-black-50 f-size-2em font-weight-bolder">
                    <i class="fas fa-cart-plus"></i> Add a Product
                </el-button>

                <div class="ml-auto">
                    <el-button
                        v-if="(order && order.id) && !isApproved"
                        @click="deleteStockOrder"
                        size="mini">
                        Delete
                    </el-button>

                    <el-button
                        @click="backToList"
                        type="danger"
                        size="mini">
                        {{ isNotDraft ? 'Close' : 'Cancel' }}
                    </el-button>

                    <el-button
                        v-if="!isProcessed && !isPending"
                        @click="updateStockOrder"
                        :disabled="!hasValidOrderLine"
                        type="success"
                        size="mini">
                        Save
                    </el-button>
                </div>
            </div>
        </el-card>

        <stock-inventory-move-form
            :visible.sync="showMoveForm"
            @close="showMoveForm = false"
            :order-lines="selectedStockOrdersLines"
            :current-order="order">
        </stock-inventory-move-form>
    </div>
</template>

<script>
    import axios from 'axios'
    import cloneDeep from 'lodash/cloneDeep'
    import debounce from 'lodash/debounce'
    import * as StockOrderStatuses from '../../../constants/stock-order-statuses'

    export default {
        name: "StockInventoryForm",

        props: {
            id: {
                required: false
            }
        },

        data() {
            const columns = [
                {label: 'Product', prop: 'code', sortable: true},
                {label: 'Description', prop: 'name', sortable: false},
                {label: 'Qty Needed', prop: 'order_qty', sortable: true},
                {label: 'Qty In Stock WH', prop: 'qty_in_stock', sortable: true},
                {label: 'Pending Orders', prop: 'pending_order_count', sortable: true},
            ]

            return {
                loading: false,
                showMoveForm: false,
                focusedField: null,
                order: null,
                totalPendingCount: 0,
                columns: columns,
                stockOrder: [],
                selectedStockOrdersLines: [],
                StockOrderStatuses
            }
        },

        computed: {
            hasValidOrderLine() {
                return this.stockOrder.some(so => so.order_qty && so.id && so.code)
            },

            isNotDraft() {
                return this.order && this.order.status !== StockOrderStatuses.STATUS_DRAFT
            },

            isPending() {
                return this.order && this.order.status === StockOrderStatuses.STATUS_PENDING
            },

            isProcessed() {
                let processedStatus = [StockOrderStatuses.STATUS_APPROVED, StockOrderStatuses.STATUS_CANCELLED]

                return this.order && processedStatus.includes(this.order.status)
            },

            isApproved() {
                return this.order && StockOrderStatuses.STATUS_APPROVED === this.order.status
            },

            enableMoveButton() {
                return !!(!!this.selectedStockOrdersLines.length && this.totalPendingCount > 1)
            },

            isApprover() {
                let user = this.$root.user

                return user && user.permissions && user.permissions.stock_ordering_approver
            }
        },

        created() {
            if (this.$route.params.isNew) {
                this.generateNewOrder()
            }

            if (!this.$route.params.isNew && this.id) {
                this.showStockOrder()
            }

            this.stockOrder.push(this.generateEmptyObject())

            this.$EventBus.listen('STOCK_ORDERING_ITEMS_MOVED', items => {
                this.showStockOrder()
            })
        },

        methods: {
            /**
             * API requests
             */
            generateNewOrder() {
                this.loading = true

                this.$API.StockOrder.createStockOrder()
                .then(res => {
                    this.order = cloneDeep(res.data)
                    this.$router.replace({
                        name: 'Stock Inventory Form View',
                        params: {
                            isNewDraft: true,
                            isNew: false,
                            id: res.data.id
                        }
                    })
                })
                .catch(err => {
                    console.log(err)
                })
                .then(_ => {
                    this.loading = false
                })
            },

            showStockOrder() {
                this.loading = true

                let id = this.$route.params.id

                this.$API.StockOrder.showStockOrder(id)
                .then(res => {
                    if (res.data.data) {
                        this.order = cloneDeep(res.data.data)
                        this.stockOrder = res.data.data.order_items.map(item => {
                            item.name = item.stock_level.name
                            item.code = item.stock_level.code
                            item.qty_in_stock = item.stock_level.available_stock
                            item.pending_order_count = item.stock_level.pending_order_count
                            return item
                        })

                        if (this.isPending) {
                            this.fetchPendingTotalCount()
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

            updateStockOrder() {
                this.loading = true

                let postData = {
                    'status': StockOrderStatuses.STATUS_PENDING
                }

                this.$API.StockOrder.updateStockOrder(postData, this.order.id)
                .then(res => {
                    if (res.data.data) {
                        this.order = cloneDeep(res.data.data)

                        this.notify('Stock order updated.')
                    }
                })
                .catch(err => {
                    console.log(err)

                    this.notify('Failed to update stock order.', 'error')
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            deleteStockOrder() {
                this.$confirm('Are you sure you want to delete this order? This action is irreversible.', 'Confirmation', {
                    confirmButtonText: 'Yes, I\'m sure!',
                    cancelButtonText: 'Nope, cancel!',
                    type: 'error'
                })
                .then(() => {
                    this.loading = true

                    this.$API.StockOrder.deleteStockOrder(this.order.id)
                    .then(res => {
                        this.notify('Stock order deleted.')

                        setTimeout(_ => {
                            this.navigateBack()
                        }, 200)
                    })
                    .catch(err => {
                        console.log(err)

                        this.notify('Failed to delete stock order.', 'error')
                    })
                    .finally(_ => {
                        this.loading = false
                    })
                })
                .catch(() => {})
            },

            cancelOrder() {
                this.$confirm('Are you sure you want to cancel this order? This action is irreversible.', 'Confirmation', {
                    confirmButtonText: 'Yes, I\'m sure!',
                    cancelButtonText: 'Nope, cancel!',
                    type: 'error'
                })
                .then(() => {
                    this.loading = true

                    this.$API.StockOrder.cancelOrder(this.order.id)
                    .then(res => {
                        if (res.data.data) {
                            this.order = cloneDeep(res.data.data)

                            this.notify('Stock order cancelled.')
                        }
                    })
                    .catch(err => {
                        console.error(err)

                        this.notify('Failed to cancel stock order.', 'error')
                    })
                    .finally(_ => {
                        this.loading = false
                    })
                })
                .catch(() => {})
            },

            moveToNewOrder() {
                this.$confirm('Are you sure you want to this items to a new order? This action is irreversible.', 'Confirmation', {
                    confirmButtonText: 'Yes, I\'m sure!',
                    cancelButtonText: 'Nope, I\'m not!',
                    type: 'warning'
                })
                .then(() => {
                    this.loading = true

                    let postData = {
                        order_item_ids: this.selectedStockOrdersLines.map(ol => ol.id).filter(ol => !!ol)
                    }

                    this.$API.StockOrder.moveItemsToNewOrder(postData)
                    .then(res => {
                        if (res.data.data) {
                            this.notify(res.data.message)

                            setTimeout(_ => {
                                this.showStockOrder()
                            }, 200)
                        }
                    })
                    .catch(err => {
                        console.error(err)

                        this.notify('Failed to move items.', 'error')
                    })
                    .finally(_ => {
                        this.loading = false
                    })
                })
                .catch(() => {})
            },

            approveOrder() {
                this.$confirm('Are you sure you want to approve this order? This action is irreversible.', 'Confirmation', {
                    confirmButtonText: 'Yes, I\'m sure!',
                    cancelButtonText: 'Nope, I\'m not!',
                    type: 'success'
                })
                .then(() => {
                    this.loading = true

                    this.$API.StockOrder.approveOrder(this.order.id)
                    .then(res => {
                        if (res.data.data) {
                            this.order = cloneDeep(res.data.data)

                            this.notify(res.data.message)
                        }
                    })
                    .catch(err => {
                        console.error(err)

                        this.notify('Failed to approve stock order.', 'error')
                    })
                    .finally(_ => {
                        this.loading = false
                    })
                })
                .catch(() => {})
            },

            createNewOrderLine(orderLine) {
                this.$API.StockOrder.addNewOrderLine(orderLine)
                .then(res => {
                    if (res.data) {
                        orderLine.id = res.data.id
                        orderLine.created_at = res.data.created_at
                        orderLine.created_by = res.data.created_by

                        this.updateStockOrderItem(orderLine)

                        this.notify('New item added.')
                    }
                })
                .catch(err => {
                    console.log(err)

                    this.notify('Failed to add new item.', 'error')
                })
            },

            updateOrderLine(orderLine, id) {
                this.$API.StockOrder.updateOrderLine(orderLine, id)
                .then(res => {
                    if (res.data) {
                        orderLine.id = res.data.id
                        orderLine.updated_by = res.data.updated_by
                        orderLine.updated_at = res.data.updated_at

                        this.updateStockOrderItem(orderLine)

                        this.notify('Order item updated.')
                    }
                })
                .catch(err => {
                    console.log(err)

                    this.notify('Failed to update order item.', 'error')
                })
            },

            deleteOrderLine(orderLine) {
                this.$API.StockOrder.deleteOrderLine(orderLine.id)
                .then(res => {
                    if (res.data) {
                        let index = this.stockOrder.findIndex(so => so.id === orderLine.id)

                        if (index > -1) {
                            this.stockOrder.splice(index, 1)

                            this.notify('Order item deleted.')
                        }
                    }
                })
                .catch(err => {
                    console.log(err)

                    this.notify('Failed to delete order item.', 'error')
                })
            },

            moveOrderLines() {
                this.showMoveForm = true
            },

            searchStockLevels(searchString, cb) {
                let selectedIds = this.stockOrder.map(line => line.stock_level_id).filter(id => !!id)
                let params = {
                    searchString,
                    ids: selectedIds
                }

                axios.get(`/admin/in-house/stock-orders/search-stock-levels`, {
                    params
                })
                .then(res => {
                    cb(res.data)
                })
                .catch(err => {
                    console.log(err)
                })
            },

            fetchPendingTotalCount() {
                this.$API.StockOrder.fetchPendingTotalCount()
                .then(res => {
                    if (res.data) {
                        this.totalPendingCount = res.data
                    }
                })
                .catch(err => {
                    console.log(err)
                })
            },

            addNewOrderLine() {
                this.stockOrder.push(this.generateEmptyObject())
            },

            removeOrderLine(index, orderLine) {
                if (orderLine.id) {
                    this.deleteOrderLine(orderLine)

                    return
                }

                this.stockOrder.splice(index, 1)
            },

            generateEmptyObject() {
                return {
                    id: null,
                    stock_level_id: null,
                    stock_order_id: null,
                    code: null,
                    name: null,
                    status: null,
                    order_qty: 0,
                    qty_in_stock: 0,
                    pending_order_count: 0
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

            handleSelectItem(item) {
                if (item.pending_order_count > 0) {
                    this.$confirm(`You already have ${item.pending_order_count} pending orders for this item, do want to still add?`, 'Confirmation', {
                        confirmButtonText: 'Ok, got it!',
                        cancelButtonText: 'Nope',
                        type: 'info'
                    })
                        .then(() => {
                            this.populateItemFromSearch(item)
                        })
                        .catch(() => {})
                } else {
                    this.populateItemFromSearch(item)
                }
            },

            populateItemFromSearch(item) {
                let focusedField = this.getFocusedFieldSections()
                let index = focusedField.index

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

                orderLine.stock_order_id = this.order.id
                orderLine.stock_level_id = item.id
                orderLine.code = item.code
                orderLine.name = item.name
                orderLine.status = this.order.status
                orderLine.qty_in_stock = item.available_stock
                orderLine.pending_order_count = item.pending_order_count

                if (orderLine.id) {
                    this.updateOrderLine(orderLine, orderLine.id);
                } else {
                    this.createNewOrderLine(orderLine)
                }
            },

            updateStockOrderItem(orderLine, jump = true) {
                let focusedField = this.getFocusedFieldSections()
                let index = focusedField.index

                if (jump) {
                    let name = focusedField.name

                    let nextIndex = name === 'orderCode' ? index : index + 1
                    this.setNextFocusedField(nextIndex, name)
                }

                this.stockOrder.splice(index, 1, orderLine)
            },

            handleEnterCodeSelection(ref) {
                if (this.$refs[ref]) {
                    this.$refs[ref].activated = false
                }
            },

            handleQtyChange: debounce(function(value) {
                let focusedField = this.getFocusedFieldSections()
                let index = focusedField.index

                let orderLine = cloneDeep(this.stockOrder[index])

                // sanity check: an order line should be present for population
                if (!orderLine) {
                    console.error('Error: No order line found by the given order line index.')

                    return
                }

                if (index === this.stockOrder.length - 1 && Number(orderLine.order_qty) <= 0) {
                    return
                }

                orderLine.order_qty = Number(orderLine.order_qty)

                if (orderLine.id) {
                    this.updateOrderLine(orderLine, orderLine.id)
                } else {
                    this.createNewOrderLine(orderLine)
                }
            }, 300),

            notify(message, type = 'success') {
                this.$notify({
                    title: 'Stock Ordering',
                    message: message,
                    type: type
                })
            },

            /**
             * Table handler
             */
            handleItemSelected(val) {
                this.selectedStockOrdersLines = cloneDeep(val)
            },

            /**
             * Page navigation methods
             */
            backToList() {
                if (!this.$route.params.isNewDraft || this.isNotDraft) {
                    this.navigateBack()

                    return
                }

                this.$confirm('Are you sure you want to leave the form? The order will be saved in drafts.', 'Confirmation', {
                    confirmButtonText: 'Ok, got it!',
                    cancelButtonText: 'Nope, continue!',
                    type: 'warning'
                })
                .then(() => {
                    this.navigateBack()
                })
                .catch(() => {})
            },

            navigateBack() {
                this.$router.back()
            }
        }
    }
</script>
