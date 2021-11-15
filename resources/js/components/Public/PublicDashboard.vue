<template>
    <div class="public-accessible">
        <el-card class="box-card mb-5">
            <div class="d-flex">
                <div class="ml-auto">
                    <el-button
                        round
                        size="mini"
                        type="info">
                        Last Update: {{ lastUpdate }}
                    </el-button>
                </div>
            </div>
            <div
                v-for="item in dataWithHeader"
                class="mt-3">
                <div class="mb-2">
                    <el-button
                        round
                        size="mini"
                        type="info">
                        {{ item.name }}
                    </el-button>
                </div>
                <el-table
                    fit
                    :data="item.data"
                    :cell-class-name="cellClassNamePicker">
                    <el-table-column
                        v-for="col in item.headers"
                        :key="col.prop"
                        :label="col.label"
                        :prop="col.prop"
                        :fixed="col.prop === 'name'">
                    </el-table-column>
                </el-table>
            </div>
        </el-card>
    </div>
</template>

<script>
    import * as Shifts from '../../constants/shifts'
    import moment from "moment"
    import cloneDeep from "lodash/cloneDeep";

    export default {
        name: "PublicDashboard",

        data() {
            return {
                Shifts,
                dataWithHeader: [],
                lastUpdate: moment().format('MMM DD, YYYY hh:mm a')
            }
        },

        created() {
            this.generateHeaders()

            this.getDataPershift()

            setInterval(_ => {
                this.getDataPershift()
            }, 180000)
        },

        methods: {
            cellClassNamePicker({row, column, rowIndex, columnIndex}) {
                let property = column.property
                // ignore these properties
                if (['name', 'internet_target', 'hourly_target', 'scheduled', 'completed', 'to_be_completed'].includes(property)) {
                    return
                }

                let hourlyTarget = Number(row.hourly_target)
                let propValue = Number(row[property])

                if (hourlyTarget === propValue) {
                    return 'background-green'
                }
                if (hourlyTarget > propValue) {
                    return 'background-red'
                }
                if (hourlyTarget < propValue) {
                    return 'background-purple'
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
                        name: `Shift ${index}`,
                        data: []
                    })
                    index++
                }
            },

            getDefaultHeaders() {
                return [
                    {label: 'Roller', prop: 'name', sortable: false, showOverflowTooltip: false},
                    {label: 'Target', prop: 'internet_target', sortable: false, showOverflowTooltip: false},
                    {label: 'HT', prop: 'hourly_target', sortable: false, showOverflowTooltip: false},
                    {label: 'Scheduled', prop: 'scheduled', sortable: false, showOverflowTooltip: false},
                    {label: 'Completed', prop: 'completed', sortable: false, showOverflowTooltip: false},
                    {label: 'To be Completed', prop: 'to_be_completed', sortable: false, showOverflowTooltip: false},
                ]
            },

            generateShiftHeaders(shift, index) {
                let headers = []
                let {start, end} = this.getShiftStartEnd(shift, index)
                let startMoment = moment(start)
                let endMoment = moment(end)

                while (startMoment < endMoment) {
                    let label = `${startMoment.format('HH')} - ${startMoment.clone().add(1, 'hour').format('HH')}`
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
                let hourNow = moment().hour()
                let now = (hourNow < 6 && hourNow > 0) ? moment().subtract(1, 'day').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD')
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
                    let nowAddOne = moment().add(1, 'day').format('YYYY-MM-DD')
                    start = `${start} ${shift[0]}`
                    end = `${nowAddOne} ${shift[1]}`
                }

                return {start, end}
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

        .cell {
            font-weight: 800;
        }
    }
</style>
