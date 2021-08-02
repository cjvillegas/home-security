<template>
    <el-card class="box-card mt-3">
        <h4>{{ getProductName }} Order Progress Status</h4>

        <el-timeline class="mt-4">
            <el-timeline-item
                v-for="process in getProcessListWithCount"
                :key="process.id"
                :timestamp="process.label"
                placement="top"
                size="large"
                :icon="getStepIcon(process.count)"
                :type="getStepType(process.count)">
                <el-card>
                    <p v-if="process.count === totalOrderCount">All {{ process.count }} item/s are done with this step.</p>
                    <p v-else>There are {{ process.count }} item/s in this process.</p>
                </el-card>
            </el-timeline-item>
        </el-timeline>

    </el-card>
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
            }
        },
        data() {
            return {
                ProductProcessCodes,
                ProductBlindTypes,
                timelineProcesses: [],
                isTimelineEvaluated: false,
                activeProduct: null
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
                            this.activeProduct = x
                            this.timelineProcesses = this.findAndSort(x)

                            return
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
            getStepType(stepCount) {
                if (stepCount === 0) {
                    return ''
                }
                else if (stepCount < this.totalOrderCount) {
                    return 'primary'
                }
                else if (stepCount === this.totalOrderCount) {
                    return 'success'
                }

                return ''
            },
            getStepIcon(stepCount) {
                if (stepCount === 0) {
                    return 'fas fa-angle-double-right'
                }
                else if (stepCount < this.totalOrderCount) {
                    return 'fas fa-cogs'
                }
                else if (stepCount === this.totalOrderCount) {
                    return 'fas fa-check-double'
                }

                return 'fas fa-angle-double-right'
            }
        },
        computed: {
            getProcessListWithCount() {
                let processes = []

                if (this.timelineProcesses && this.timelineProcesses.length) {
                    let ordersInProcess = this.orders.filter(or => or.scanners.length)
                    for (let x of this.timelineProcesses) {
                        processes.push({
                            label: x.name,
                            key: x.id,
                            count: ordersInProcess.filter(or => {
                                return or.scanners.some(sc => sc.processid === x.barcode)
                            }).length
                        })
                    }
                }

                return processes
            },
            totalOrderCount() {
                if (this.orders && this.orders.length) {
                    return this.orders.length
                }

                return 0
            },
            getProductName() {
                if (this.activeProduct) {
                    return this.$StringService.ucwords(this.activeProduct.replace(/_/g, ' '))
                }

                return ''
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
