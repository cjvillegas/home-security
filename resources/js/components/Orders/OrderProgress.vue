<template>
    <div>
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
                    class="mr-2 mt-2">
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

        <order-timeline
            :processList="timeLineProcesses"
            :visible.sync="showTimelineForm"
            @close="closeForm">
        </order-timeline>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "OrderProgress",
        props: {
            order: {
                required: true,
            },
            orders: {},
            processSequences: {}
        },

        data() {
            return {
                showTrackingForm: false,
                showTimelineForm: false,
                timeLineProcesses: [],
            }
        },

        methods: {
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
            ...mapGetters(['processes']),

            getProcessListWithCount() {
                let processes = []

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
                            let scanners = []
                            let count = orderInSequence.filter(or => {
                                scanners = [...or.scanners.filter(sc => sc.processid === step.process.barcode), ...scanners]
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
