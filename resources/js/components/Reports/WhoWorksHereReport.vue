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
                            value-format="yyyy-MM-dd"
                            placeholder="Pick a day"
                            class="w-100">
                        </el-date-picker>

                        <global-employee-selector
                            class="mt-3"
                            :value.sync="filters.employees"
                            is-multiple>
                        </global-employee-selector>

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

            <el-tabs
                v-model="activeTab"
                type="border-card"
                class="mb-5 mt-3">
                <el-tab-pane
                    v-for="product in products"
                    :key="product.key"
                    :name="product.key"
                    :label="product.name"
                    v-loading="loading"
                    lazy>
                    <div
                        v-for="(process, index) in product.processes"
                        :key="process.code"
                        :class="`${index > 0 ? 'mt-5' : ''}`">
                        <el-divider
                            content-position="left"
                            class="mt-3">
                            <h5>{{ process.name }}</h5>
                        </el-divider>
                        <el-table
                            fit
                            :data="getTableData(product, process)">
                            <template
                                slot="empty">
                                <el-empty
                                    class="p-0"
                                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                                </el-empty>
                            </template>

                            <el-table-column
                                v-for="col in tableColumns"
                                :key="col.prop"
                                :prop="col.prop"
                                :label="col.label"
                                :show-overflow-tooltip="col.showOverflowTooltip"
                                :sortable="col.sortable">
                                <template slot-scope="scope">
                                    <template v-if="['clock_in', 'clock_out', 'first_scan', 'last_scan'].includes(col.prop)">
                                        {{ scope.row[col.prop] | fixDateByFormat("MMM DD, YYYY hh:mm A") }}
                                    </template>
                                    <template v-else-if="col.prop === 'employee'">
                                        {{ scope.row[col.prop] | ucWords }}
                                    </template>
                                    <template v-else>
                                        {{ scope.row[col.prop] }}
                                    </template>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </el-card>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep"

    export default {
        name: "WhoWorksHereReport",

        data() {
            return {
                activeTab: 'rollers',
                loading: false,
                filters: {
                    date: moment().format('YYYY-MM-DD'),
                    employees: []
                },
                tableColumns: [
                    {prop: 'employee', label: 'Employee', showOverflowTooltip: true, sortable: true},
                    {prop: 'clock_num', label: 'Clock No', showOverflowTooltip: true, sortable: true},
                    {prop: 'clock_in', label: 'Clock In', showOverflowTooltip: true, sortable: true},
                    {prop: 'clock_out', label: 'Clock Out', showOverflowTooltip: true, sortable: true},
                    {prop: 'first_scan', label: 'First Scan', showOverflowTooltip: true, sortable: true},
                    {prop: 'last_scan', label: 'Last Scan', showOverflowTooltip: true, sortable: true}
                ],
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
            }
        },

        computed: {
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
                        let firstScanner = employee.scanners.first_scan
                        let lastScanner = employee.scanners.last_scan
                        let clock_in = employee.attendance.clock_in
                        let clock_out = employee.attendance.clock_out
                        let processCode = firstScanner.processid
                        let productIndex = productCodes.findIndex(pc => pc.some(code => code === processCode))

                        if (products[productIndex]) {
                            let processIndex = products[productIndex].processes.findIndex(p => p.code === processCode)

                            if (processIndex > -1) {
                                let newItem = {
                                    employee: employee.fullname,
                                    clock_num: employee.clock_num,
                                    first_scan: firstScanner.scannedtime,
                                    last_scan: lastScanner ? lastScanner.scannedtime : null,
                                    clock_in: clock_in.swiped_at,
                                    clock_out: clock_out ? clock_out.swiped_at : null,
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

                let filters = cloneDeep(this.filters)
                this.sanitizeFilter(filters)

                this.$API.Reports.whoWorksHere(filters)
                .then(res => {
                    if (Array.isArray(res.data)) {
                        this.workAttendance = res.data
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            sanitizeFilter(filters) {
                if (filters.employees.every(e => e === null)) {
                    delete filters.employees
                }
            },

            getTableData(product, process) {
                let base = this.processedData.find(e => e.key === product.key)
                let data = []

                if (base) {
                    let processedData = base.processes.find(pr => pr.code === process.code)

                    if (processedData) {
                        data = processedData.data
                    }
                }

                return data
            }
        }
    }
</script>
