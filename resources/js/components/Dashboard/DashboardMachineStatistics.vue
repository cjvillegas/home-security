<template>
    <div class="sbg-container">
        <el-button-group>
            <el-button
                @click="setView('vertical')"
                :type="isViewVertical ? 'primary' : 'info'">
                Vertical
            </el-button>
            <el-button
                @click="setView('venetian')"
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
                        <el-table
                            fit
                            v-loading="loading.vertical"
                            :data="distributedData.vertical.today">
                            <template
                                slot="empty">
                                <el-empty
                                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                                </el-empty>
                            </template>

                            <el-table-column
                                v-for="col in verticalMachines"
                                :key="col.id"
                                :prop="col.id"
                                :label="col.name">
                                <template slot-scope="scope">
                                    <template v-if="col.id === 'name'">
                                        {{ scope.row[col.id] | ucWords }}
                                    </template>

                                    <template v-else>
                                        <template>
                                            <div>Processed Blinds: {{ (scope.row[col.id] ? scope.row[col.id].processed_blinds : 0) | numFormat }}</div>
                                            <div>Processed Orders: {{ (scope.row[col.id] ? scope.row[col.id].processed_orders : 0) | numFormat }}</div>
                                        </template>
                                    </template>
                                </template>
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
                        <el-table
                            fit
                            v-loading="loading.vertical"
                            :data="distributedData.vertical.yesterday">
                            <template
                                slot="empty">
                                <el-empty
                                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                                </el-empty>
                            </template>

                            <el-table-column
                                v-for="col in verticalMachines"
                                :key="col.id"
                                :prop="col.id"
                                :label="col.name">
                                <template slot-scope="scope">
                                    <template v-if="col.id === 'name'">
                                        {{ scope.row[col.id] | ucWords }}
                                    </template>

                                    <template v-else>
                                        <template>
                                            <div>Processed Blinds: {{ (scope.row[col.id] ? scope.row[col.id].processed_blinds : 0) | numFormat }}</div>
                                            <div>Processed Orders: {{ (scope.row[col.id] ? scope.row[col.id].processed_orders : 0) | numFormat }}</div>
                                        </template>
                                    </template>
                                </template>
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
                        <el-table
                            fit
                            v-loading="loading.venetian"
                            :data="distributedData.venetian.today">
                            <template
                                slot="empty">
                                <el-empty
                                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                                </el-empty>
                            </template>

                            <el-table-column
                                v-for="col in venetiansMachines"
                                :key="col.id"
                                :prop="col.id"
                                :label="col.name">
                                <template slot-scope="scope">
                                    <template v-if="col.id === 'name'">
                                        {{ scope.row[col.id] | ucWords }}
                                    </template>

                                    <template v-else>
                                        <template>
                                            <div v-if="!['GL632', 'GL70'].includes(col.name) || col.id === 'total'">
                                                Processed Blinds: {{ (scope.row[col.id] ? scope.row[col.id].processed_blinds : 0) | numFormat }}
                                            </div>
                                            <div v-if="['GL632', 'GL70'].includes(col.name) || col.id === 'total'">
                                                Headrail Cut: {{ (scope.row[col.id] ? scope.row[col.id].headrail_cut : 0) | numFormat }}
                                            </div>
                                            <div>Processed Orders: {{ (scope.row[col.id] ? scope.row[col.id].processed_orders : 0) | numFormat }}</div>
                                        </template>
                                    </template>
                                </template>
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
                        <!-- Venetian Yesterday -->
                        <el-table
                            fit
                            v-loading="loading.venetian"
                            :data="distributedData.venetian.yesterday">
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
                                <template slot-scope="scope">
                                    <template v-if="col.id === 'name'">
                                        {{ scope.row[col.id] | ucWords }}
                                    </template>

                                    <template v-else>
                                        <template>
                                            <div v-if="!['GL632', 'GL70'].includes(col.name) || col.id === 'total'">
                                                Processed Blinds: {{ (scope.row[col.id] ? scope.row[col.id].processed_blinds : 0) | numFormat }}
                                            </div>
                                            <div v-if="['GL632', 'GL70'].includes(col.name) || col.id === 'total'">
                                                Headrail Cut: {{ (scope.row[col.id] ? scope.row[col.id].headrail_cut : 0) | numFormat }}
                                            </div>
                                            <div>Processed Orders: {{ (scope.row[col.id] ? scope.row[col.id].processed_orders : 0) | numFormat }}</div>
                                        </template>
                                    </template>
                                </template>
                            </el-table-column>
                        </el-table>
                        <!-- End of Venetian Yesterday-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep";

    export default {
        name: "DashboardMachineStatistics",

        data() {
            return {
                loading: {
                    vertical: false,
                    venetian: false
                },
                machines: [],
                distributedData: {
                    vertical: {
                        today: [],
                        yesterday: []
                    },
                    venetian: {
                        today: [],
                        yesterday: []
                    },
                },
                loadedTracker: {
                    venetian: false,
                    vertical: false
                },
                activeView: 'vertical'
            }
        },

        computed: {
            isViewVertical() {
                return (this.activeView === 'vertical')
            },

            verticalMachines() {
                let names = this.getMachineNameByType('vertical')
                let machines = this.machines.filter(machine => names.includes(machine.name))
                    .map(m => {
                        m.id = m.id.toString()

                        return m
                    })

                machines.push({
                    id: 'total',
                    name: "Total"
                })

                machines.unshift({
                    id: 'name',
                    name: "Shift"
                })

                return machines
            },

            venetiansMachines() {
                let names = this.getMachineNameByType('venetian')
                let machines = this.machines.filter(machine => names.includes(machine.name))
                    .map(m => {
                        m.id = m.id.toString()

                        return m
                    })

                machines.push({
                    id: 'total',
                    name: "Total"
                })

                machines.unshift({
                    id: 'name',
                    name: "Shift"
                })

                return machines
            }
        },

        created() {
            this.allMachines()

            this.machineStatistics('vertical')
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
                this.loading[type] = true

                let start = moment().subtract(1, 'day').format('YYYY-MM-DD') + ' 00:00:00'
                let end = moment().format('YYYY-MM-DD') + ' 23:59:59'

                let filters = {
                    dates: [start, end],
                    type,
                    machines: this.getMachineNameByType(type)
                }

                this.$API.Reports.dashboardMachineStatistics(filters)
                .then(res => {
                    this.distributedData[type].today = this.plotData(res.data.today)
                    this.distributedData[type].yesterday = this.plotData(res.data.yesterday)
                    this.loadedTracker[type] = true
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading[type] = false
                })
            },

            plotData(data) {
                let dataSet = []

                for (let rootKey in data) {
                    dataSet.push(data[rootKey])
                }

                return dataSet
            },

            setView(type) {
                this.activeView = type

                /**
                 * We will only load the data of each view once per page reload so we need to check
                 * if the view already loaded its data from the server and prevent it to call the API
                 * again if the data has been loaded
                 */
                if (!this.loadedTracker[type]) {
                    this.machineStatistics(type)
                }
            },

            getMachineNameByType(type) {
                let machines = ['A3-VB-TS2', 'GL123', 'GL119', 'GL34', 'GL52']

                if (type === 'venetian') {
                    machines = ['GL632', 'GL70', 'GL62', 'GL622', 'GL627']
                }

                return machines
            }
        }
    }
</script>
