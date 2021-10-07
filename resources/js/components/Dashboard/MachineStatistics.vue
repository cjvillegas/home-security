<template>
    <div class="sbg-container">
        <el-button-group>
            <el-button
                @click="activeView = 'vertical'"
                :type="isViewVertical ? 'primary' : 'info'">
                Vertical
            </el-button>
            <el-button
                @click="activeView = 'venetian'"
                :type="isViewVertical ? 'info' : 'primary'">
                Venetian
            </el-button>
        </el-button-group>

        <div class="mt-3">
            <div v-if="activeView === 'vertical'">
                <div>
                    <div class="sbg-header-container">
                        Vertical - Today
                    </div>
                    <div>
                        <!-- Vertical Today -->
                        <el-table>
                            <template
                                slot="empty">
                                <el-empty
                                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                                </el-empty>
                            </template>

                            <el-table-column
                                v-for="col in verticalMachines"
                                :key="col.id"
                                :label="col.name">
                            </el-table-column>
                        </el-table>
                        <!-- End of Vertical Today-->
                    </div>
                </div>

                <div class="mt-5">
                    <div class="sbg-header-container">
                        Vertical - Yesterday
                    </div>
                    <div>
                        <!-- Vertical Yesterday -->
                        <el-table>
                            <template
                                slot="empty">
                                <el-empty
                                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                                </el-empty>
                            </template>

                            <el-table-column
                                v-for="col in verticalMachines"
                                :key="col.id"
                                :label="col.name">
                            </el-table-column>
                        </el-table>
                        <!-- End of Vertical Yesterday-->
                    </div>
                </div>
            </div>

            <div v-else>
                <div>
                    <div class="sbg-header-container">
                        Venetian - Today
                    </div>
                    <div>
                        <!-- Venetian Today -->
                        <el-table>
                            <template
                                slot="empty">
                                <el-empty
                                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                                </el-empty>
                            </template>

                            <el-table-column
                                v-for="col in venetiansMachines"
                                :key="col.id"
                                :label="col.name">
                            </el-table-column>
                        </el-table>
                        <!-- End of Venetian Today-->
                    </div>
                </div>

                <div class="mt-5">
                    <div class="sbg-header-container">
                        Venetian - Yesterday
                    </div>
                    <div>
                        <!-- Venetian Today -->
                        <el-table>
                            <template
                                slot="empty">
                                <el-empty
                                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                                </el-empty>
                            </template>

                            <el-table-column
                                v-for="col in venetiansMachines"
                                :key="col.id"
                                :label="col.name">
                            </el-table-column>
                        </el-table>
                        <!-- End of Venetian Today-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep";

    export default {
        name: "MachineStatistics",

        data() {
            return {
                loading: false,
                machines: [],
                distributedData: {
                    verticalToday: [],
                    verticalYesterday: [],
                    venetianToday: [],
                    venetianYesterday: []
                },
                activeView: 'vertical'
            }
        },

        computed: {
            isViewVertical() {
                return (this.activeView === 'vertical')
            },


            verticalMachines() {
                let names = ['A3-VB-TS2', 'GL123', 'GL119', 'GL34', 'GL52']
                let machines = this.machines.filter(machine => names.includes(machine.name))
                machines.push({
                    id: 'total',
                    name: "Total"
                })

                machines.unshift({
                    id: 'shift',
                    name: "Shift"
                })

                return machines
            },

            venetiansMachines() {
                let names = ['GL632', 'GL70', 'GL62', 'GL622', 'GL627']
                let machines = this.machines.filter(machine => names.includes(machine.name))
                machines.push({
                    id: 'total',
                    name: "Total"
                })

                machines.unshift({
                    id: 'shift',
                    name: "Shift"
                })

                return machines
            }
        },

        created() {
            this.allMachines()

            this.machineStatistics('vertical')
            this.machineStatistics('venetian')
        },

        methods: {
            allMachines() {
                this.$API.Machine.allMachines()
                .then(res => {
                    this.machines = cloneDeep(res.data)
                })
                .catch(err => {
                    console.log(err)
                })
            },

            machineStatistics(type) {
                this.loading = true

                let machines = ['A3-VB-TS2', 'GL123', 'GL119', 'GL34', 'GL52']
                if (type === 'venetian') {
                    machines = ['GL632', 'GL70', 'GL62', 'GL622', 'GL627']
                }

                let start = moment().subtract(1, 'day').format('YYYY-MM-DD') + ' 00:00:00'
                let end = moment().format('YYYY-MM-DD') + ' 23:59:59'

                let filters = {
                    dates: [start, end],
                    type,
                    machines
                }

                this.$API.Reports.dashboardMachineStatistics(filters)
                .then(res => {

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
