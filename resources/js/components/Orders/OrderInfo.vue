<template>
    <div>
        <el-descriptions
            v-if="hasOrderDetails"
            class="margin-top"
            :column="4"
            size="medium"
            direction="vertical"
            border>
            <template slot="extra">
                <el-button
                    @click="showTimelineDialog = true"
                    size="small"
                    type="default"
                    class="mr-2">
                    Operations Timeline
                </el-button>
            </template>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-boxes"></i>
                    Order
                </template>
                {{ order_details.order_no | numFormat }}
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-user"></i>
                    Customer
                </template>
                {{ order_details.customer | valueForEmptyText }}
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-retweet"></i>
                    Customer Ref.
                </template>
                {{ order_details.customer_ref | valueForEmptyText }}
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-cubes"></i>
                    Total Blinds
                </template>
                <el-button
                    @click="openTotalBlinds">
                    {{ order_details.total_blinds | numFormat }}
                </el-button>
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-address-card"></i>
                    Order Status
                </template>
                {{ getOrderStatus }}
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-address-card"></i>
                    Entered By
                </template>
                {{ order_details.order_entered_by | ucWords }}
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-calendar"></i>
                    Ordered
                </template>
                {{ order_details.ordered_at | fixDateByFormat }}
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-calendar-alt"></i>
                    Requested Date
                </template>
                {{ order_details.requested_at | fixDateByFormat }}
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-clipboard-list"></i>
                    Planned Date
                </template>
                <el-button
                    @click="showPlannedWorkModal = true"
                    type="text"
                    class="pt-0 pb-0">
                    Click here to view planned works.
                </el-button>
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-calendar-check"></i>
                    Packed Date
                </template>
                <el-button
                    @click="showPackedOrdersModal = true"
                    type="text"
                    class="pt-0 pb-0">
                    Click here to view packed orders data.
                </el-button>
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-calendar-plus"></i>
                    Despatched Date
                </template>
                <el-button
                    @click="showTrackingForm = true"
                    type="text"
                    class="pt-0 pb-0">
                    Click here to view order tracking.
                </el-button>
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-address-card"></i>
                    Invoiced Date
                </template>
                --:--
            </el-descriptions-item>

            <el-descriptions-item>
                <template slot="label">
                    <i class="fas fa-address-card"></i>
                    Invoiced No.
                </template>
                --:--
            </el-descriptions-item>
        </el-descriptions>

        <el-empty
            v-else
            description="No Order Details Found. The order might be deleted or not existing in your company.">
        </el-empty>

        <order-timeline
            :processList="timelineProcesses"
            :visible.sync="showTimelineForm"
            @close="closeTimelineProcessForm">
        </order-timeline>

        <order-view-planned-work
            ref="plannedWork"
            :visible.sync="showPlannedWorkModal"
            :order="order"
            :update-parent="true"
            @close="showPlannedWorkModal = false">
        </order-view-planned-work>

        <order-view-packed-orders
            ref="packedOrders"
            v-if="order"
            :scanners="packedData"
            :visible.sync="showPackedOrdersModal"
            @close="showPackedOrdersModal = false">
        </order-view-packed-orders>

        <order-view-operations-timeline
            ref="operationsTimeline"
            v-if="order"
            :order="order"
            :orders="orders"
            :process-sequences="processSequences"
            :visible.sync="showTimelineDialog"
            @close="showTimelineDialog = false">
        </order-view-operations-timeline>

        <!-- Order Trackings -->
        <order-tracking
            :order="order"
            :order-trackings="orderTrackings"
            :visible.sync="showTrackingForm"
            @close="showTrackingForm = false">
        </order-tracking>
        <!-- End of Order Trackings -->

        <order-view-total-blinds
            :order_no="order_no"
            :visible.sync="showTotalBlindsModal"
            @close="showTotalBlindsModal = false">
        </order-view-total-blinds>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import {mapActions, mapGetters} from "vuex";
import OrderViewTotalBlinds from './OrderView/OrderViewTotalBlinds.vue';

    export default {
  components: { OrderViewTotalBlinds },
        name: "OrderInfo",

        props: {
            loadOrder: {
                type: Boolean,
                default: false
            },

            order: {
                required: true
            }
        },

        data() {
            return {
                showTimelineForm: false,
                showTrackingForm: false,
                showPlannedWorkModal: false,
                showPackedOrdersModal: false,
                showTimelineDialog: false,
                showTotalBlindsModal: false,
                timelineProcesses: [],
                order_details: null,
                orders: [],
                processSequences: [],
                orderTrackings: [],
                order_no: null
            }
        },

        computed: {
            ...mapGetters(['processes']),

            hasOrderDetails() {
                return this.order_details && this.order_details.id
            },

            packedData() {
                if (this.order && this.order.scanners && this.order.scanners.length) {
                    return this.order.scanners.filter(sc => ['P1012', 'P1014'].includes(sc.processid))
                }

                return []
            },

            getOrderStatus() {
                if (!this.order || !this.order.scanners) {
                    return '--:--'
                }

                if (!this.order.scanners.length) {
                    return 'Not Started'
                }

                // if an order has order trackings
                if (this.orderTrackings.length) {
                    return 'Order Shipped'
                }

                let isPartiallyManufactured = this.getProcessListWithCount.some(p => !p.processes.every(pr => pr.count === p.totalCount))

                if (isPartiallyManufactured) {
                    return 'Partially Manufactured'
                }

                let isFullyManufactured = this.getProcessListWithCount.some(p => {
                    let packingIndex = p.processes.findIndex(pr => ['P5688737', 'P1014'].includes(pr.barcode))

                    if (packingIndex > -1) {
                        let normalProcess = p.processes.filter((pr, index) => {
                            return index < packingIndex
                        })

                        if (p.processes[packingIndex] && p.processes[packingIndex].count === 0 && normalProcess.every(np => np.count === p.totalCount)) {
                            return true
                        }
                    }

                    return false
                })

                if (isFullyManufactured) {
                    return 'Fully Manufactured'
                }

                let partiallyPacked = this.getProcessListWithCount.some(p => {
                    let packingIndex = p.processes.findIndex(pr => ['P1012', 'P1014'].includes(pr.barcode))

                    if (packingIndex > -1) {
                        if (p.processes[packingIndex] && p.processes[packingIndex].count > 0 && p.processes[packingIndex].count < p.totalCount) {
                            return true
                        }
                    }

                    return false
                })

                if (partiallyPacked) {
                    return 'Partially Packed'
                }

                let fullyPacked = this.getProcessListWithCount.some(p => {
                    let packingIndex = p.processes.findIndex(pr => ['P1012', 'P1014'].includes(pr.barcode))

                    if (packingIndex > -1) {
                        if (p.processes[packingIndex] && p.processes[packingIndex].count === p.totalCount) {
                            return true
                        }
                    }

                    return false
                })

                if (fullyPacked) {
                    return 'Order Packed'
                }

                return '--:--'
            },

            getProcessListWithCount() {
                let processes = []
                let scanners = []

                if (this.processSequences && this.processSequences.length && this.orders && this.orders.length) {
                    let ordersInProcess = this.orders.filter(or => or.scanners.length)

                    for (let [index, x] of this.processSequences.entries()) {

                        let orderInSequence = ordersInProcess.filter(or => x.name.toLowerCase() === or.product_type.toLowerCase())

                        processes.push({
                            product: x.name,
                            name: this.$StringService.ucwords(x.name),
                            totalCount: this.orders.filter(or => x.name.toLowerCase() === or.product_type.toLowerCase()).length,
                            processes: []
                        })

                        for (let step of x.steps) {
                            let count = orderInSequence.filter(or => {
                                scanners = or.scanners
                                return or.scanners.some(sc => sc.processid === step.process.barcode)
                            }).length

                            let type = 'danger'
                            if (count > 1 && count < processes[index].totalCount) {
                                type = 'warning'
                            }

                            if (count === processes[index].totalCount) {
                                type = 'success'
                            }

                            processes[index].processes.push({
                                label: this.$StringService.ucwords(step.process.name),
                                scanners: scanners,
                                barcode: step.process.barcode,
                                key: step.process.id,
                                count,
                                type
                            })
                        }

                    }
                }

                return processes
            },
            totalOrderCount() {
                if (this.getProcessListWithCount && this.getProcessListWithCount.length) {
                    return this.getProcessListWithCount.reduce((acc, cur) => acc += cur.totalCount , 0)
                }

                return 0
            }
        },

        methods: {
            openTimelineDialog(processes) {
                this.timelineProcesses = processes
                this.showTimelineForm = true
            },

            closeTimelineProcessForm() {
                this.showTimelineForm = false
            },

            getOrdersByOrderNo() {
                this.$API.Orders.getOrdersByOrderNo(this.order.order_no)
                .then(res => {
                    this.orders = res.data
                })
                .catch(err => {
                    console.log(err)
                })
            },

            getOrderProcessSequences() {
                this.$API.Orders.getOrderProcessSequences(this.order.order_no)
                .then(res => {
                    this.processSequences = res.data
                })
                .catch(err => {
                    console.log(err)
                })
            },

            getOrderTrackingsData() {
                let apiUrl = `/admin/orders/trackings`

                this.loading = true

                axios.post(apiUrl, {'order_no' : this.order.order_no})
                .then((response) => {
                    this.orderTrackings = response.data.orderTrackings
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = true
                })
            },

            openTotalBlinds() {
                this.order_no = this.order.order_no
                this.showTotalBlindsModal = true
            }
        },

        watch: {
            order: {
                handler() {
                    this.order_details = cloneDeep(this.order)

                    if (this.order && this.order.order_no) {
                        this.getOrdersByOrderNo()
                        this.getOrderProcessSequences()
                        this.getOrderTrackingsData()
                    }
                },
                deep: true,
                immediate: true
            }
        }
    }
</script>
