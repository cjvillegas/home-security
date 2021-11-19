<template>
    <div class="public-accessible">
        <el-card class="box-card mb-5">
            <div class="d-flex">
                <div class="ml-auto">
                    <el-button
                        round
                        size="mini"
                        type="info"
                        class="page-info">
                        Clock: {{ clock }}
                    </el-button>

                    <el-button
                        round
                        size="mini"
                        type="info"
                        class="page-info">
                        View Change In: {{ countDownDisplay }}
                    </el-button>

                    <el-button
                        round
                        size="mini"
                        type="info"
                        class="page-info">
                        Last Update: {{ lastUpdate }}
                    </el-button>
                </div>
            </div>

            <el-tabs
                @tab-click="tabChanged"
                v-model="activeTab"
                type="border-card"
                class="mt-4">
                <el-tab-pane
                    v-for="item in dataWithHeader"
                    :key="item.tab_name"
                    :name="item.tab_name"
                    :label="item.name">
                    <el-table
                        border
                        fit
                        size="medium"
                        :data="item.data"
                        :cell-class-name="cellClassNamePicker"
                        class="mt-3 mb-3">
                        <el-table-column
                            v-for="col in item.headers"
                            :key="col.prop"
                            :label="col.label"
                            :prop="col.prop"
                            :fixed="col.prop === 'name'"
                            align="center">
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
            </el-tabs>

            <el-descriptions
                class="mt-3"
                title="Legend"
                :column="4"
                size="medium"
                border>
<!--                <el-descriptions-item>-->
<!--                    <template slot="label">-->
<!--                        T-->
<!--                    </template>-->
<!--                    <span class="font-weight-bold">Target</span>-->
<!--                </el-descriptions-item>-->

                <el-descriptions-item>
                    <template slot="label">
                        S
                    </template>
                    <span class="font-weight-bold">Scheduled</span>
                </el-descriptions-item>

                <el-descriptions-item>
                    <template slot="label">
                        C
                    </template>
                    <span class="font-weight-bold">Completed Scanners</span>
                </el-descriptions-item>

                <el-descriptions-item>
                    <template slot="label">
                        TC
                    </template>
                    <span class="font-weight-bold">Left to do</span>
                </el-descriptions-item>
            </el-descriptions>
        </el-card>
    </div>
</template>

<script>
    import * as Shifts from '../../constants/shifts'
    import moment from "moment"

    export default {
        name: "PublicDashboard",

        data() {
            return {
                Shifts,
                dataWithHeader: [],
                lastUpdate: moment().format('MMM DD, YYYY hh:mm a'),
                activeTab: 'shift_1',
                nextChangeDate: moment().add(1.5, 'minutes'),
                countDownDisplay: '01:30',
                intervals: {
                    shiftChanger: null,
                },
                clock: moment().format('MMM DD, YYYY hh:mm:ss a')
            }
        },

        created() {
            this.generateHeaders()

            this.getDataPershift()

            // infinitely call the request to populate the table data with 10 minutes interval
            setInterval(_ => {
                this.getDataPershift()
            }, 600000)

            this.runChangerShiftInterval()

            // count down changer
            setInterval(_ => {
                this.countDownDisplay = this.countDownTimer()
            }, 1000)

            // clock
            setInterval(_ => {
                this.clock = moment().format('MMM DD, YYYY hh:mm:ss a')
            }, 1000)
        },

        methods: {
            cellClassNamePicker({row, column, rowIndex, columnIndex}) {
                let property = column.property

                if (property === 'name') {
                    return 'custom-roller-column'
                }


                // ignore these properties
                if (['name', 'team_target', 'hourly_target', 'scheduled', 'completed', 'to_be_completed'].includes(property)) {
                    return
                }

                let hourlyTarget = Number(row.hourly_target)
                let propValue = Number(row[property])

                if (property === 'percentage') {
                    propValue = row.percentage
                    let percentage = propValue.substring(0, propValue.length - 1)
                    let number = Number(percentage)

                    if (number >= 0 && number <= 60) {
                        return 'background-red'
                    }

                    if (number >= 61 && number <= 99) {
                        return 'background-orange'
                    }

                    if (number >= 100) {
                        return 'background-green'
                    }
                }

                if (hourlyTarget < propValue) {
                    return 'background-green'
                }

                if (hourlyTarget >= propValue) {
                    return 'background-red'
                }
            },

            getDataPershift() {
                let index = 1
                for (let shift of Shifts.SHIFT_TIME_LIST) {
                    this.getData(shift, index)
                    index++
                }

                this.lastUpdate = moment().format('MMM DD, YYYY hh:mm a')
            },

            getData(shift, index) {
                this.loading = true

                let {start, end} = this.getShiftStartEnd(shift, index)
                let params = {
                    start,
                    end,
                    index
                }

                this.$API.Public.getDashboardData(params)
                    .then(res => {
                        this.dataWithHeader[index - 1].data = res.data
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            generateHeaders() {
                let index = 1
                for (let shift of Shifts.SHIFT_TIME_LIST) {
                    let header = [...this.getDefaultHeaders(), ...this.generateShiftHeaders(shift, index)]
                    this.dataWithHeader.push({
                        headers: header,
                        name: `Roller Shift ${index}`,
                        tab_name: `shift_${index}`,
                        data: []
                    })
                    index++
                }
            },

            getDefaultHeaders() {
                return [
                    {label: 'P', prop: 'name', sortable: false, showOverflowTooltip: false},
                    // {label: 'T', prop: 'team_target', sortable: false, showOverflowTooltip: false},
                    {label: 'HT', prop: 'hourly_target', sortable: false, showOverflowTooltip: false},
                    {label: 'S', prop: 'scheduled', sortable: false, showOverflowTooltip: false},
                    {label: 'C', prop: 'completed', sortable: false, showOverflowTooltip: false},
                    {label: 'TC', prop: 'to_be_completed', sortable: false, showOverflowTooltip: false},
                    {label: '%', prop: 'percentage', sortable: false, showOverflowTooltip: false},
                ]
            },

            generateShiftHeaders(shift, index) {
                let headers = []
                let {start, end} = this.getShiftStartEnd(shift, index)
                let startMoment = moment(start)
                let endMoment = moment(end)

                while (startMoment < endMoment) {
                    let label = `${startMoment.format('HH')}`
                    let key = `${startMoment.format('HH')}-${startMoment.clone().add(1, 'hour').format('HH')}`
                    let header = {
                        label: label,
                        prop: key,
                        sortable: false,
                        showOverflowTooltip: false
                    }

                    headers.push(header)

                    startMoment = startMoment.add(1, 'hour')
                }

                return headers
            },

            getShiftStartEnd(shift, index) {
                let date = moment().format('YYYY-MM-DD')
                let hourNow = moment(date).hour()
                let now = (hourNow < 6 && hourNow > 0) ? moment(date).subtract(1, 'day').format('YYYY-MM-DD') : moment(date).format('YYYY-MM-DD')
                let start = now
                let end = now

                if (index === 1) {
                    start = `${start} ${shift[0]}`
                    end = `${end} ${shift[1]}`
                }
                if (index === 2) {
                    start = `${start} ${shift[0]}`
                    end = `${end} ${shift[1]}`
                }
                if (index === 3) {
                    let nowAddOne = moment(date).add(1, 'day').format('YYYY-MM-DD')
                    start = `${start} ${shift[0]}`
                    end = `${nowAddOne} ${shift[1]}`
                }

                return {start, end}
            },

            shiftChanger() {
                let shiftIndex = Number(this.activeTab.split('_')[1])

                if (shiftIndex === 3) {
                    this.activeTab = 'shift_1'
                } else {
                    this.activeTab = `shift_${shiftIndex + 1}`
                }

                this.nextChangeDate = moment().add(1.5, 'minutes')
            },

            countDownTimer() {
                let toDateUnix = this.nextChangeDate.unix()
                let nowUnix = moment().unix()
                let duration = moment.duration((toDateUnix - nowUnix) * 1000)

                let seconds = duration.seconds() < 10 ? `0${duration.seconds()}` : duration.seconds()

                return `0${duration.minutes()}:${seconds}`
            },

            tabChanged() {
                this.nextChangeDate = moment().add(1.5, 'minutes')

                clearInterval(this.intervals.shiftChanger)

                this.runChangerShiftInterval()
            },

            runChangerShiftInterval() {
                // tab changer with 5 minutes interval
                this.intervals.shiftChanger = setInterval(_ => {
                    this.shiftChanger()
                }, 90000)
            }
        }
    }
</script>

<style lang="scss" scoped>
    .c-app,
    .c-wrapper,
    .c-body,
    .c-main,
    .container-fluid,
    .public-accessible {
        .el-card,
        .el-card__body {
            height: 100vh;
        }
    }
</style>
