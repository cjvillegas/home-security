<template>
    <div v-loading="loading">
        <div class="text-right">
            <el-button
                @click="getDespatchDepartmentAnalytics"
                class="mb-3"
                size="mini">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </el-button>
        </div>

        <el-card class="box-card">
            <div slot="header" class="clearfix d-flex">
                <h4>Despatch Department - Today's Progress</h4>
            </div>

            <div class="row">
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge-list
                        title="Shift 1"
                        :data="formattedProgress.today_shift_1"
                        :machines="machines.today"
                        :machineData="todayMachineCounterData"
                        :shift="1">
                    </global-gauge-list>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge-list
                        title="Shift 2"
                        :data="formattedProgress.today_shift_2"
                        :machines="machines.today"
                        :machineData="todayMachineCounterData"
                        :shift="2">
                    </global-gauge-list>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge-list
                        title="Shift 3"
                        :data="formattedProgress.today_shift_3"
                        :machines="machines.today"
                        :machineData="todayMachineCounterData"
                        :shift="3">
                    </global-gauge-list>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge-list
                        title="Total"
                        :data="formattedProgress.today_total"
                        :machines="machines.today"
                        :machineData="todayTotalMachineBoxes"
                        :shift="3">
                    </global-gauge-list>
                </div>
            </div>
        </el-card>

        <el-card class="box-card mt-5">
            <div slot="header" class="clearfix d-flex">
                <h4>Despatch Department - Yesterday's Progress</h4>
            </div>

            <div class="row">
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge-list
                        title="Shift 1"
                        :data="formattedProgress.yesterday_shift_1"
                        :machines="machines.yesterday"
                        :machineData="yesterdayMachineCounterData"
                        :shift="1">
                    </global-gauge-list>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge-list
                        title="Shift 2"
                        :data="formattedProgress.yesterday_shift_2"
                        :machines="machines.yesterday"
                        :machineData="yesterdayMachineCounterData"
                        :shift="2">
                    </global-gauge-list>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge-list
                        title="Shift 3"
                        :data="formattedProgress.yesterday_shift_3"
                        :machines="machines.yesterday"
                        :machineData="yesterdayMachineCounterData"
                        :shift="3">
                    </global-gauge-list>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge-list
                        title="Total"
                        :data="formattedProgress.yesterday_total"
                        :machines="machines.yesterday"
                        :machineData="yesterdayTotalMachineBoxes"
                        :shift="3">
                    </global-gauge-list>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'

    export default {
        name: "DashboardDespatchDepartment",
        data() {
            return {
                loading: false,
                total: {
                    shift_1_P1012_today: 0,
                    shift_1_P1012_yesterday: 0,
                    shift_1_P1013_today: 0,
                    shift_1_P1013_yesterday: 0,
                    shift_1_P1014_today: 0,
                    shift_1_P1014_yesterday: 0,
                    shift_2_P1012_today: 0,
                    shift_2_P1012_yesterday: 0,
                    shift_2_P1013_today: 0,
                    shift_2_P1013_yesterday: 0,
                    shift_2_P1014_today: 0,
                    shift_2_P1014_yesterday: 0,
                    shift_3_P1012_today: 0,
                    shift_3_P1012_yesterday: 0,
                    shift_3_P1013_today: 0,
                    shift_3_P1013_yesterday: 0,
                    shift_3_P1014_today: 0,
                    shift_3_P1014_yesterday: 0,
                    total_P1012_today: 0,
                    total_P1012_yesterday: 0,
                    total_P1013_today: 0,
                    total_P1013_yesterday: 0,
                    total_P1014_today: 0,
                    total_P1014_yesterday: 0,
                },
                machines: {
                    todayTotal: [],
                    yesterDay: [],
                },
                todayMachineCounterData: [],
                yesterdayMachineCounterData: [],

                todayTotalMachineBoxes: [],
                yesterdayTotalMachineBoxes: []
            }
        },
        created() {
            this.getDespatchDepartmentAnalytics()
            this.fetchMachineStatistics()
        },
        methods: {
            getDespatchDepartmentAnalytics() {
                this.loading = true

                this.$API.Reports.getDespatchDepartmentAnalytics()
                    .then(res => {
                        this.total = res.data
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            fetchMachineStatistics() {
                let apiUrl = `/admin/reports/dashboard-machine-statistics`

                axios.get(apiUrl)
                .then((response) => {
                    this.todayMachineCounterData = response.data.todayMachineCounterData
                    this.yesterdayMachineCounterData = response.data.yesterdayMachineCounterData
                    this.machines.today = response.data.todayMachines
                    this.machines.yesterday = response.data.yesterdayMachines
                    this.todayTotalMachineBoxes = response.data.todayTotalMachineBoxes
                    this.yesterdayTotalMachineBoxes = response.data.yesterdayTotalMachineBoxes
                })
            },
        },
        computed: {
            formattedProgress() {
                const shifts = ['shift_1', 'shift_2', 'shift_3', 'total']
                const processes = ['P1012', 'P1013', 'P1014']
                let data = {}
                let defaultValues = [
                    {key: 'P1012', label: 'Louvres Packed', count: 0},
                    {key: 'P1013', label: 'Headrail Picked', count: 0},
                    {key: 'P1014', label: 'Machine Packed', count: 0},
                ]

                for (let shift of shifts) {
                    let today = cloneDeep(defaultValues)
                    let yesterday = cloneDeep(defaultValues)

                    for (let process of processes) {
                        let todayIndex = today.findIndex(t => t.key === process)
                        let yesterdayIndex = yesterday.findIndex(t => t.key === process)

                        if (todayIndex > -1) {
                            today[todayIndex].count = this.total[`${shift}_${process}_today`]
                        }

                        if (yesterdayIndex > -1) {
                            yesterday[yesterdayIndex].count = this.total[`${shift}_${process}_yesterday`]
                        }
                    }

                    data[`today_${shift}`] = today
                    data[`yesterday_${shift}`] = yesterday
                }

                return data
            }
        }
    }
</script>
