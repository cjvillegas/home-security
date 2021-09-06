<template>
    <div>
        <el-button v-if="trackings.length != 0"
            @click="showTrackingForm = true"
            effect="dark"
            size="small"
            type="success"
            class="mr-2">
            Tracking Available
        </el-button>
        <el-tag
            v-else
            effect="dark"
            size="medium"
            type="info"
            class="mr-2">
            No Tracking Available
        </el-tag>
        <div
            v-for="product in getProcessListWithCount"
            :key="product.product">
            <el-divider>{{ product.name }}</el-divider>

            <div>
                <el-tag
                    v-for="process in product.processes"
                    :key="process.id"
                    :type="process.type"
                    effect="dark"
                    size="medium"
                    class="mr-2">
                    <b>{{ process.label }}: {{ process.count }} / {{ totalOrderCount }}</b>
                </el-tag>
                <el-button
                    @click="openTimelineDialog(product.processes)"
                    effect="dark"
                    size="small"
                    type="default"
                    class="mr-2">
                    Timeline
                </el-button>
            </div>
        </div>

        <order-tracking
            v-if="trackings.length != 0"
            :visible.sync="showTrackingForm"
            :trackings="trackings"
            @close="closeForm">
        </order-tracking>
        <order-timeline
            :processList="timeLineProcesses"
            :visible.sync="showTimelineForm"
            @close="closeForm">
        </order-timeline>
    </div>
</template>

<script>
    import * as ProductProcessCodes from '../../constants/ProductProcessCodes'
    import * as ProductBlindTypes from '../../constants/ProductBlindTypes'
    export default {
        name: "OrderProgress",
        props: {
            orders: {
                required: true,
            },
            processes: {
                required: true,
                type: Array
            },
            trackings: {
                required: true,
                type: Array
            }
        },
        data() {
            return {
                ProductProcessCodes,
                ProductBlindTypes,
                timelineProcesses: [],
                isTimelineEvaluated: false,
                showTrackingForm: false,
                showTimelineForm: false,
                timeLineProcesses: [],
            }
        },
        created() {
            this.getTimelineProcesses()
        },
        methods: {
            getTimelineProcesses() {
                // if this method is already executed, cancel execution
                if (this.isTimelineEvaluated) {
                    return
                }

                if (this.orders && this.orders.length) {
                    this.isTimelineEvaluated = true
                    for (let x in this.ProductBlindTypes) {
                        let codes = this.ProductBlindTypes[x]
                        let fromBlindType = codes.find(code => this.orders.some(or => or.blind_type === code))
                        if (fromBlindType) {
                            this.timelineProcesses.push({
                                productName: x,
                                processes: this.findAndSort(x),
                                blindTypes: codes
                            })
                        }
                    }
                }
            },
            findAndSort(product) {
                let codeList = this.ProductProcessCodes[product]
                let processes = []

                for (let x of codeList) {
                    let process = this.processes.find(pr => pr.barcode === x)

                    if (process) {
                        processes.push(process)
                    }
                }

                return processes
            },
            openTimelineDialog(processes) {
                this.timeLineProcesses = processes
                this.showTimelineForm = true
            },
            closeForm() {
                this.showTrackingForm = false
                this.showTimelineForm = false
            }
        },
        computed: {
            getProcessListWithCount() {
                let processes = []
                let scanners = []
                if (this.timelineProcesses && this.timelineProcesses) {
                    let ordersInProcess = this.orders.filter(or => or.scanners.length)
                    for (let [index, x] of this.timelineProcesses.entries()) {
                        let ordersInBlindType = ordersInProcess.filter(or => x.blindTypes.includes(or.blind_type))
                        processes.push({
                            product: x.productName,
                            name: this.$StringService.ucwords(x.productName.replace(/_/g, ' ')),
                            totalCount: this.orders.filter(or => x.blindTypes.some(bt => bt === or.blind_type)).length,
                            processes: []
                        })


                        for (let y of x.processes) {
                            let count = ordersInBlindType.filter(or => {
                                scanners = or.scanners
                                return or.scanners.some(sc => sc.processid === y.barcode)
                            }).length

                            let type = 'danger'
                            if (count > 1 && count < processes[index].totalCount) {
                                type = 'warning'
                            }

                            if (count === processes[index].totalCount) {
                                type = 'success'
                            }
                            processes[index].processes.push({
                                label: this.$StringService.ucwords(y.name),
                                scanners: scanners,
                                barcode: y.barcode,
                                key: y.id,
                                count,
                                type
                            })
                        }

                    }
                }

                console.log(processes)
                return processes
            },
            totalOrderCount() {
                if (this.getProcessListWithCount && this.getProcessListWithCount.length) {
                    return this.getProcessListWithCount.reduce((acc, cur) => acc += cur.totalCount , 0)
                }

                return 0
            }
        },
        watch: {
            orders: {
                handler() {
                    this.getTimelineProcesses()
                },
                immediate: true,
                deep: true
            }
        }
    }
</script>

<style>
    .el-timeline-item__node--large {
        left: -10px;
        padding: 15px;
        top: -10px;
    }
</style>
