<template>
    <el-card class="box-card mt-3">
        <div
            v-for="product in getProcessListWithCount"
            :key="product.product">
            <el-divider>{{ product.name }}</el-divider>

            <div class="d-flex">
                <el-tag
                    v-for="process in product.processes"
                    :key="process.id"
                    :type="process.type"
                    effect="dark"
                    size="medium"
                    class="mr-2">
                    <b>{{ process.label }}: {{ process.count }}</b>
                </el-tag>
            </div>
        </div>
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
            }
        },
        computed: {
            getProcessListWithCount() {
                let processes = []

                if (this.timelineProcesses && this.timelineProcesses) {
                    let ordersInProcess = this.orders.filter(or => or.scanners.length)
                    for (let [index, x] of this.timelineProcesses.entries()) {
                        processes.push({
                            product: x.productName,
                            name: this.$StringService.ucwords(x.productName.replace(/_/g, ' ')),
                            totalCount: this.orders.filter(or => x.blindTypes.some(bt => bt === or.blind_type)).length,
                            processes: []
                        })
                        for (let y of x.processes) {
                            let count = ordersInProcess.filter(or => {
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
                                key: y.id,
                                count,
                                type
                            })
                        }

                    }
                }

                return processes
            },
            totalOrderCount() {
                if (this.orders && this.orders.length) {
                    return this.orders.length
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
