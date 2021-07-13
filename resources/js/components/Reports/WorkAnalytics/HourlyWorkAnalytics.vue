<template>
    <el-card v-loading="loading">
        <div>
            <label
                for="shift"
                class="ml-3">Shift: </label>
            <el-select
                id="shift"
                v-model="filters.shift"
                filterable
                placeholder="Select shift to show">
                <el-option label="Select All" :value="null"></el-option>
                <el-option
                    v-for="shft in pageData.shifts"
                    :key="shft.id"
                    :label="shft.name"
                    :value="shft.id">
                </el-option>
            </el-select>

            <label
                for="employees"
                class="ml-3">Employees: </label>
            <el-select
                id="employees"
                v-model="filters.employees"
                filterable
                multiple
                collapse-tags
                placeholder="Select employees to show"
                style="width: 250px"
                @change="handleEmployeeSelection">
                <el-option label="Select All" :value="null"></el-option>
                <el-option
                    v-for="employees in pageData.employees"
                    :key="employees.id"
                    :label="$StringService.ucwords(employees.fullname)"
                    :value="employees.id">
                </el-option>
            </el-select>

            <label
                for="process"
                class="ml-3">Process: </label>
            <el-select
                id="process"
                v-model="filters.processes"
                filterable
                multiple
                collapse-tags
                placeholder="Select process to show"
                @change="handleProcessSelection"
                style="width: 250px">
                <el-option label="Select All" :value="null"></el-option>
                <el-option
                    v-for="process in pageData.processes"
                    :key="process.id"
                    :label="process.name"
                    :value="process.id">
                </el-option>
            </el-select>

            <div class="pull-right">
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

        <div class="mt-3">
            <label
                for="date"
                class="ml-3">Date: </label>
            <el-date-picker
                @change="fetchReports"
                :clearable="false"
                v-model="filters.date"
                :picker-options="{
                    disabledDate(time) {
                        return time.getTime() > Date.now();
                    }
                }"
                type="date"
                placeholder="Pick a day">
            </el-date-picker>

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

        <line-chart
            ref="lineChart"
            :styles="myStyles"
            class="mt-3"
            :chart-data="plottedData"
            :options="options">
        </line-chart>

        <div class="mt-3 text-center font-weight-bolder">Total Blinds From The Selected Filters: {{ shiftTotal }}</div>
    </el-card>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep"
    import fileExporter from '../../../mixins/fileExporter'

    export default {
        name: "HourlyWorkAnalytics",
        mixins: [fileExporter],
        props: {
            pageData: {
                required: true,
                type: Object
            }
        },
        data() {
            return {
                loading: false,
                filters: {
                    shift: null,
                    team: null,
                    processes: [null],
                    employees: [null],
                    legend: 'process',
                    date: moment().format('YYYY-MM-DD')
                },
                options: {
                    title: {
                        display: true,
                        text: 'Hourly Work Analytics',
                    },
                    subtitle: {
                        display: true,
                        text: 'Fucking',
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                },
                scanners: [],
                shiftTotal: 0
            }
        },
        created() {
            this.fetchReports()
        },
        methods: {
            handleProcessSelection(processes) {
                if (processes[processes.length - 1] === null) {
                    this.filters.processes = [null]
                } else {
                    let index = this.filters.processes.findIndex(em => em === null)

                    if (index > -1) {
                        this.filters.processes.splice(index, 1)
                    }
                }
            },
            handleEmployeeSelection(employees) {
                if (employees[employees.length - 1] === null) {
                    this.filters.employees = [null]
                } else {
                    let index = this.filters.employees.findIndex(em => em === null)

                    if (index > -1) {
                        this.filters.employees.splice(index, 1)
                    }
                }
            },
            fetchReports() {
                let sod = moment(this.filters.date).hour(6).startOf('hour')
                let eod = sod.clone().add(1, 'day').startOf('hour').format('YYYY-MM-DD HH:mm')
                sod = sod.format('YYYY-MM-DD HH:mm')

                this.loading = true

                this.$API.Reports.fetchHourlyAnalytics(sod, eod)
                    .then(res => {
                        this.scanners = cloneDeep(res.data)
                        if (this.$refs.lineChart) {
                            this.options.title.text = `Hourly Work Analytics (Total blind for the day ${this.scanners.length})`
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
            getLabels() {
                let {sod, eod} = this.getSodAndEod()

                let columns = []
                // get the times of the specified range
                while (sod <= eod) {
                    columns.push(sod.format('HH:mm'))

                    sod = sod.add(1, 'hour')
                }

                return columns
            },
            getSodAndEod() {
                // get the shift or the time frame to which the graph should base
                let shift = this.pageData.shifts.find(shift => shift.id === this.filters.shift)

                let shiftStart = 6
                let shiftEnd = 6
                // sanity check if a shift exist
                if (shift) {
                    // get the shift start and end time
                    shiftStart = this.$DateService.getHoursFromTimeFormat(shift.shift_start)
                    shiftEnd = this.$DateService.getHoursFromTimeFormat(shift.shift_end)
                }

                // define the SOD and EOD
                let sod = moment(this.filters.date).hour(shiftStart).startOf('hour')
                let eod = moment(this.filters.date).hour(shiftEnd).startOf('hour')

                // if the shift is 3, push the eod to the next day for it's their end of shift
                if (!shift || shift.id === 3) {
                    eod.add(1, 'day')
                }

                return {sod, eod}
            },
            getLegends() {
                let legends = cloneDeep(this.filters.legend === 'employee' ? this.pageData.employees : this.pageData.processes)

                if (this.filters.legend === 'employee') {
                    if (!this.filters.employees.some(em => em === null)) {
                        legends = legends.filter(leg => this.filters.employees.some(em => em === leg.id))
                    }

                    legends = legends.map(emp => {
                        emp.label = this.$StringService.ucwords(emp.fullname)

                        return emp
                    })
                }
                if (this.filters.legend === 'process') {
                    if (!this.filters.processes.some(em => em === null)) {
                        legends = legends.filter(leg => this.filters.processes.some(pr => pr === leg.id))
                    }

                    legends = legends.map(prc => {
                        prc.label = this.$StringService.ucwords(prc.name)

                        return prc
                    })
                }

                return legends
            },
            exportToFile() {
                this.loading = true

                let headers = [].concat(this.filters.legend === 'process' ? ['Process Name', 'Process Barcode'] : ['Employee Name', 'Employee Barcode'], this.getLabels())
                let data = this.plottedData.datasets.reduce((acc, cur) => {
                    acc.push([].concat([cur.label, cur.barcode], cur.data))

                    return acc
                }, [])

                data.push([])

                data.push([].concat(['', 'Total'], this.plottedData.setTotal))

                this.$API.Exports.exportHourlyWorkAnalyticsReport(headers, data)
                    .then(res => {
                        this.exporter('xlsx', `Hourly Work Analytics Report`, res.data)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
        },
        computed: {
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
                        barcode: x.barcode,
                        data: [],
                        fill: false,
                        backgroundColor: x.color,
                        borderColor: x.color,
                    }

                    let localScanners = []
                    if (this.filters.legend === 'process') {
                        localScanners = scanners.filter(scanner => {
                            let isInProcess = scanner.processid === x.barcode
                            let isByEmployee = true

                            if (!this.filters.employees.some(em => em === null)) {
                                isByEmployee = this.filters.employees.some(em => scanner.employee && scanner.employee.id === em)
                            }

                            return isInProcess && isByEmployee
                        })
                    }
                    if (this.filters.legend === 'employee') {
                        localScanners = scanners.filter(scanner => {
                            let isByEmployee = scanner.employeeid === x.barcode
                            let isInProcess = true

                            if (!this.filters.processes.some(prc => prc === null)) {
                                isInProcess = this.filters.processes.some(prc => scanner.process && scanner.process.id === prc)
                            }

                            return isByEmployee && isInProcess
                        })
                    }

                    let data = []
                    let {sod, eod} = this.getSodAndEod()
                    let sodCopy = sod.clone().add(1,'hour')

                    // get the times of the specified range
                    let index = 0
                    while (sodCopy <= eod && sod <= eod) {
                        let count = localScanners.filter((scanner, index) => {
                            return moment(scanner.scannedtime, 'MM/DD/YYYY HH:mm:ss').isBetween(sod, sodCopy, null, '[)')
                        }).length

                        data.push(count)
                        setTotal[index] = setTotal[index] !== undefined ? setTotal[index] + count : count

                        sod = sod.add(1, 'hour')
                        sodCopy = sodCopy.unix() === eod.unix() ? sodCopy : sodCopy.add(1, 'hour')

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
        }
    }
</script>
