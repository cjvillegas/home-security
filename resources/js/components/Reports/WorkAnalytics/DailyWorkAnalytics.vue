<template>
    <el-card v-loading="loading">
        <div>
            <div class="pull-right">
                <el-button
                    @click="openFilter">
                    <i class="fas fa-filter"></i> Filters
                </el-button>

                <el-button
                    type="success"
                    @click="exportToFile">
                    <i class="fas fa-file-export"></i> Export
                </el-button>

                <el-button @click="fetchReports">
                    <i v-if="!loading" class="fas fa-sync-alt"></i>
                    <i v-else class="el-icon-loading"></i>
                </el-button>
            </div>
        </div>

        <div>
            <label
                for="legend"
                class="ml-3">Legend: </label>
            <el-select
                id="legend"
                v-model="filters.legend"
                filterable
                placeholder="Select process to show">
                <el-option label="Process" value="process"></el-option>
                <el-option label="Employee" value="employee"></el-option>
            </el-select>
        </div>

        <div>
            <work-analytics-filter-visual
                :filters="filters">
            </work-analytics-filter-visual>
        </div>


        <line-chart
            ref="lineChart"
            :styles="myStyles"
            class="mt-3"
            :chart-data="plottedData"
            :options="options">
        </line-chart>

        <div class="mt-3 text-center font-weight-bolder">Total Blinds From The Selected Filters: {{ shiftTotal }}</div>

        <work-analytics-filter
            @filtersUpdated="updateFilters"
            :is-date-range="true"
            :visible.sync="showFilterDialog"
            :filters.sync="filters"
            @close="showFilterDialog = false">
        </work-analytics-filter>
    </el-card>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep"
    import fileExporter from '../../../mixins/fileExporter'
    import {mapGetters} from "vuex";

    export default {
    name: "DailyWorkAnalytics",

    mixins: [fileExporter],

    data() {
        return {
            loading: false,
            showFilterDialog: false,
            filters: {
                shift: null,
                team: null,
                processes: [null],
                employees: [null],
                legend: 'process',
                date: [moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD')]
            },
            options: {
                title: {
                    display: true,
                    text: 'Daily Work Analytics',
                },
                subtitle: {
                    display: true,
                    text: 'Fucking',
                },
                legend: {
                    position: 'right'
                },
                responsive: true,
                maintainAspectRatio: false,
            },
            scanners: [],
            shiftTotal: 0
        }
    },

    computed: {
            ...mapGetters(['employees', 'shifts', 'processes']),

            myStyles () {
                return {
                    height: `700px`,
                    position: 'relative'
                }
            },

            plottedData() {
                this.shiftTotal = 0
                let legends = this.getLegends()
                let dataSet = {
                    labels: this.getLabels(),
                    datasets: [],
                    setTotal: [],
                }
                let setTotal = []
                let scanners = cloneDeep(this.scanners)

                for (let x of legends) {
                    let dataset = {
                        label: x.label,
                        data: [],
                        fill: false,
                        backgroundColor: x.color,
                        borderColor: x.color
                    }

                    let localScanners = []
                    if (this.filters.legend === 'process') {
                        localScanners = scanners.filter(scanner => {
                            let isInProcess = scanner.processid === x.barcode
                            let isByEmployee = true

                            if (!this.filters.employees.some(em => em === null)) {
                                isByEmployee = this.filters.employees.some(em => scanner.employee_id === em)
                            }

                            return isInProcess && isByEmployee
                        })
                    }
                    if (this.filters.legend === 'employee') {
                        localScanners = scanners.filter(scanner => {
                            let isByEmployee = scanner.employeeid === x.barcode
                            let isInProcess = true

                            if (!this.filters.processes.some(prc => prc === null)) {
                                isInProcess = this.filters.processes.some(prc => scanner.process_id === prc)
                            }

                            return isByEmployee && isInProcess
                        })
                    }

                    let data = []
                    let {sod, eod} = this.getSodAndEod()
                    let sodCopy = sod.clone().add(1,'day')

                    // get the times of the specified range
                    let index = 0
                    while (sodCopy <= eod && sod <= eod) {
                        let count = localScanners.filter((scanner, index) => {
                            return moment(scanner.scannedtime, 'MM/DD/YYYY HH:mm:ss').isBetween(sod, sodCopy, null, '[)')
                        }).length

                        data.push(count)
                        setTotal[index] = setTotal[index] !== undefined ? setTotal[index] + count : count

                        sod = sod.add(1, 'day')
                        sodCopy = sodCopy.unix() === eod.unix() ? sodCopy : sodCopy.add(1, 'day')

                        index++
                    }
                    this.shiftTotal += data.reduce((acc, cur) => acc += cur , 0)
                    dataset.data = data
                    dataSet.datasets.push(dataset)
                }

                dataSet.labels = dataSet.labels.map((label, index) => {
                    label = `${label} (${setTotal[index] ? setTotal[index] : 0})`

                    return label
                })
                dataSet.setTotal = setTotal

                return dataSet
            },
        },

    created() {
        this.fetchReports()
    },

    methods: {
        updateFilters() {
            this.fetchReports()
        },

        openFilter() {
            this.showFilterDialog = true
        },

        fetchReports() {
            let {sod, eod} = this.getSodAndEod()
            sod = sod.format('YYYY-MM-DD HH:mm')
            eod = eod.format('YYYY-MM-DD HH:mm')

            let parameters = cloneDeep(this.filters)
            parameters.start = sod
            parameters.end = eod

            this.sanitizeParameters(parameters)

            this.loading = true

            this.$API.Reports.fetchWorkAnalytics(parameters)
                .then(res => {
                    this.scanners = cloneDeep(res.data)
                    if (this.$refs.lineChart) {
                        this.options.title.text = `Daily Work Analytics (Total blind for the selected period ${this.scanners.length})`
                        this.$refs.lineChart.renderChart(this.plottedData, this.options)
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
        },

        sanitizeParameters(parameters) {
            if (parameters.processes.every(pr => pr === null)) {
                delete parameters.processes
            }

            if (parameters.employees.every(em => em === null)) {
                delete parameters.employees
            }
        },

        getLabels() {
            let {sod, eod} = this.getSodAndEod()

            let columns = []
            // get the times of the specified range
            while (sod <= eod) {
                columns.push(sod.format('YYYY-MM-DD'))

                sod = sod.add(1, 'day')
            }

            return columns
        },

        getSodAndEod() {
            // define the SOD and EOD
            let sod = moment(this.filters.date[0]).hour(6).startOf('hour')
            let eod = moment(this.filters.date[1]).add(1, 'day').hour(6).startOf('hour')

            return {sod, eod}
        },

        getLegends() {
            let legends = cloneDeep(this.filters.legend === 'employee' ? this.employees : this.processes)
            let filters = cloneDeep(this.filters.legend === 'employee' ? this.filters.employees : this.filters.processes)

            if (!filters.some(filter => filter === null)) {
                legends = legends.filter(leg => filters.some(em => em === leg.barcode))
            }

            legends = legends.map(leg => {
                leg.label = this.$StringService.ucwords(this.filters.legend === 'employee' ? leg.fullname : leg.name)

                return leg
            })

            return legends
        },

        exportToFile() {
            this.loading = true

            let headers = [].concat(this.filters.legend === 'process' ? ['Process Name', 'Process Barcode'] : ['Employee Name', 'Employee Barcode'], this.getLabels())
            let data = this.plottedData.datasets.reduce((acc, cur) => {
                acc.push([].concat([cur.label, cur.barcode], cur.data))

                return acc
            }, [])

            data.push([].concat(['', 'Total'], this.plottedData.setTotal))

            this.$API.Exports.exportDailyWorkAnalyticsReport(headers, data)
                .then(res => {
                    this.exporter('xlsx', `Daily Work Analytics Report`, res.data)
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

<style scoped>

</style>
