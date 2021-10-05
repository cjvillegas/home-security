<template>
    <div v-loading="loading">
        <global-page-header title="Who Works Here"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div class="ml-auto">
                    <global-filter-box>
                        <label>Select Date</label>
                        <el-date-picker
                            v-model="filters.date"
                            type="date"
                            clearable
                            placeholder="Pick a day"
                            class="w-100">
                        </el-date-picker>

                        <el-button
                            @click="getWhoWorksHereData"
                            :disabled="disableApplyFilterButton"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>
                </div>
            </div>

            <el-collapse
                v-model="activeNames"
                class="mt-3">
                <el-collapse-item
                    v-for="(prod, index) in processedData"
                    :key="prod.key"
                    :name="prod.key">
                    <template slot="title">
                        <h4>{{ prod.name }}</h4>
                    </template>
                    <sbg-process-info
                        v-if="!prod.processes.every(pr => !pr.data.length)"
                        :timeline="prod.processes">
                    </sbg-process-info>

                    <el-empty
                        v-else
                        :image-size="100"
                        description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                    </el-empty>
                </el-collapse-item>
            </el-collapse>
        </el-card>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep"

    export default {
        name: "WhoWorksHereReport",

        data() {
            return {
                loading: false,
                filters: {
                    date: moment().format('YYYY-MM-DD')
                },
                products: [
                    {
                        name: 'Rollers',
                        key: 'rollers',
                        processes: [
                            {
                                code: 'P1000',
                                name: 'Tube Cut',
                                data: []
                            },
                            {
                                code: 'P1005',
                                name: 'Fabric Cut',
                                data: []
                            },
                            {
                                code: 'P1003',
                                name: 'Attaching',
                                data: []
                            },
                            {
                                code: 'P1004',
                                name: 'Sewing In',
                                data: []
                            },
                            {
                                code: 'P1002',
                                name: 'Testing/Packing',
                                data: []
                            },
                            {
                                code: 'P1014',
                                name: 'Machine Packing',
                                data: []
                            }
                        ]
                    },
                    {
                        name: 'Roll Express',
                        key: 'roll_express',
                        processes: [
                            {
                                code: 'P1022',
                                name: 'Saws',
                                data: []
                            },
                            {
                                code: 'P1023',
                                name: 'Sewing',
                                data: []
                            },
                            {
                                code: 'P1024',
                                name: 'Packing',
                                data: []
                            },
                            {
                                code: 'P1014',
                                name: 'Machine Packing',
                                data: []
                            },
                        ]
                    },
                    {
                        name: 'Venetian\'s',
                        key: 'venetians',
                        processes: [
                            {
                                code: 'P1018',
                                name: 'Headrail Cut',
                                data: []
                            },
                            {
                                code: 'P1020',
                                name: 'Stats Machine',
                                data: []
                            },
                            {
                                code: 'P1019',
                                name: 'Assembly',
                                data: []
                            },
                            {
                                code: 'P1021',
                                name: 'Testing/Packing',
                                data: []
                            },
                            {
                                code: 'P1014',
                                name: 'Machine Packing',
                                data: []
                            }
                        ]
                    },
                    {
                        name: 'Vertical',
                        key: 'vertical',
                        processes: [
                            {
                                code: 'P1000',
                                name: 'Headrail Tube Cut',
                                data: []
                            },
                            {
                                code: 'P1025',
                                name: 'Louvres Manufacturing',
                                data: []
                            },
                            {
                                code: 'P1001',
                                name: 'Headrail Assembly',
                                data: []
                            },
                            {
                                code: 'P1012',
                                name: 'Louvres Packed',
                                data: []
                            },
                            {
                                code: 'P1013',
                                name: 'Headrail Pickup',
                                data: []
                            },
                            {
                                code: 'P1014',
                                name: 'Machine Packing',
                                data: []
                            }
                        ]
                    }
                ],
                workAttendance: [],
                activeNames: ['rollers']
            }
        },

        computed: {
            handleChange() {

            },

            disableApplyFilterButton() {
                return this.loading
            },

            processedData() {
                let products = cloneDeep(this.products)
                let productCodes = products.reduce((acc, cur) => {
                    acc.push(cur.processes.map(p => p.code))

                    return acc
                }, [])

                if (this.workAttendance && !!this.workAttendance.length) {
                    for (let employee of this.workAttendance) {
                        let firstScanner = employee.scanners[0]
                        let lastScanner = employee.scanners[employee.scanners.length - 1]
                        let clock_in = employee.attendance[0]
                        let clock_out = employee.attendance[employee.attendance.length - 1]
                        let processCode = firstScanner.processid
                        let productIndex = productCodes.findIndex(pc => pc.some(code => code === processCode))

                        if (products[productIndex]) {
                            let processIndex = products[productIndex].processes.findIndex(p => p.code === processCode)

                            if (processIndex > -1) {
                                let newItem = {
                                    employee: employee.fullname,
                                    clock_num: employee.clock_num,
                                    first_scan: firstScanner.scannedtime,
                                    last_scan: lastScanner.scannedtime,
                                    clock_in: clock_in.swiped_at,
                                    clock_out: clock_out.swiped_at,
                                }

                                products[productIndex].processes[processIndex].data.push(newItem)
                            }
                        }
                    }
                }

                return products
            }
        },

        created() {
            this.getWhoWorksHereData()
        },

        methods: {
            getWhoWorksHereData() {
                this.loading = true

                this.$API.Reports.whoWorksHere(this.filters)
                .then(res => {
                    this.workAttendance = res.data
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            }
        }
    }
</script>
