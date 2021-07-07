<template>
    <div class="work-analytics">
        <el-card v-loading="loading">
            <div>
                <label for="shift">Shift: </label>
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

                <label for="team" class="ml-3">Team: </label>
                <el-select
                    id="team"
                    v-model="filters.team"
                    filterable
                    placeholder="Select team to show">
                    <el-option label="Select All" :value="null"></el-option>
                    <el-option
                        v-for="team in pageData.teams"
                        :key="team.id"
                        :label="team.name"
                        :value="team.id">
                    </el-option>
                </el-select>

                <label for="team" class="ml-3">Process: </label>
                <el-select
                    id="process"
                    v-model="filters.process"
                    filterable
                    placeholder="Select process to show">
                    <el-option label="Select All" :value="null"></el-option>
                    <el-option
                        v-for="process in pageData.processes"
                        :key="process.barcode"
                        :label="process.name"
                        :value="process.barcode">
                    </el-option>
                </el-select>

                <el-button
                    class="pull-right"
                    @click="fetchReports">
                    <i v-if="!loading" class="fas fa-sync-alt"></i>
                    <i v-else class="el-icon-loading"></i>
                </el-button>
            </div>

            <line-chart
                :styles="myStyles"
                class="mt-3"
                :chart-data="plottedData"
                :options="options">
            </line-chart>
        </el-card>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'

    export default {
    name: "WorkAnalyticsIndex",
    props: {
        pageData: {
            required: true,
            type: Object
        }
    },
    data() {
        let shift = this.pageData.shifts.find(s => s.id)

        return {
            loading: false,
            filters: {
                shift: shift ? shift.id : null,
                team: null,
                process: null
            },
            options: {
                title: {
                    display: true,
                    text: 'Hourly Work Analytics',
                },
                responsive: true,
                maintainAspectRatio: false,
            },
            scanners: []
        }
    },
    created() {
        this.fetchReports()
    },
    methods: {
        fetchReports() {
            let sod = moment().hour(6).startOf('hour')
            let eod = sod.clone().add(1, 'day').startOf('hour').format('YYYY-MM-DD HH:mm')
            sod = sod.format('YYYY-MM-DD HH:mm')

            this.loading = true

            this.$API.Reports.fetchHourlyAnalytics(sod, eod)
            .then(res => {
                this.scanners = cloneDeep(res.data)
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
            let sod = moment().hour(shiftStart).startOf('hour')
            let eod = moment().hour(shiftEnd).startOf('hour')

            // if the shift is 3, push the eod to the next day for it's their end of shift
            if (!shift || shift.id === 3) {
                eod.add(1, 'day')
            }

            return {sod, eod}
        }
    },
    computed: {
        myStyles () {
            return {
                height: `500px`,
                position: 'relative'
            }
        },
        plottedData() {
            let teams = this.pageData.teams.filter(team => team.id === this.filters.team || !this.filters.team)
            let dataSet = {
                labels: this.getLabels(),
                datasets: []
            }
            let scanners = cloneDeep(this.scanners)

            for (let x of teams) {
                let dataset = {
                    label: x.name,
                    data: [],
                    fill: false,
                    backgroundColor: x.color,
                    borderColor: x.color
                }

                let localScanners = scanners.filter(scanner => scanner.employee && scanner.employee.team && scanner.employee.team.id === x.id)
                // filter for specific process
                if (this.filters.process) {
                    localScanners = localScanners.filter(scanner => scanner.processid === this.filters.process)
                }

                let data = []
                let {sod, eod} = this.getSodAndEod()

                // get the times of the specified range
                while (sod <= eod) {
                    let dataInDates = localScanners.filter(scanner => moment(scanner.scannedtime).isBetween(sod, eod, null, '[)'))
                    data.push(dataInDates.length)

                    sod = sod.add(1, 'hour')
                }
                dataset.data = data
                dataSet.datasets.push(dataset)
            }

            return dataSet
        },
    }

}
</script>

<style scoped>

</style>
