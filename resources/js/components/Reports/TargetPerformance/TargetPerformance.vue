<template>
    <div>
        <global-page-header title="Target Performance"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div class="ml-auto">
                    <el-button
                        @click="openFilter">
                        <i class="fas fa-filter"></i> Filters
                    </el-button>
                    <el-button
                       @click="clickExport"
                       :disabled="!canExportData"
                        type="success">
                       <i class="fas fa-file-export"></i> Export
                   </el-button>
                </div>
            </div>
            <div
                v-for="(employee, employeeKey) in performances"
                :key="employeeKey"
                class="border-bottom">
                <h4>{{ employee.employee_name }}</h4>
                <div class="row"
                    v-for="(performance, performanceKey) in employee.performances"
                    :key="performanceKey">
                    <div class="col-md-2">
                        {{ performance.process_name }}
                    </div>
                    <div class="overflow-auto col-md-10">
                        <table
                            class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="bg-secondary text-dark">Date:</th>
                                    <th
                                        v-for="(date, dateKey) in dates"
                                        :key="dateKey"
                                        class="bg-secondary text-dark">
                                        <span>
                                            <h5>{{ date| fixDateByFormat('MM-DD-YYYY') }}</h5>
                                        </span>
                                    </th>
                                    <th
                                        v-if="dates.length > 0"
                                        class="bg-warning text-dark">
                                        <span>
                                            <h5>Total</h5>
                                        </span>
                                    </th>
                                    <th
                                        v-if="dates.length > 0"
                                        class="bg-warning text-dark">
                                        <span>
                                            <h5>Performance</h5>
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="bg-success text-dark">
                                        <span> {{ tradeType(employee.is_new_joiner, employee.type ) }}</span>
                                    </td>
                                    <td
                                        v-for="(performanceDate, performanceDateKey) in performance.data"
                                        :key="performanceDateKey"
                                        class="bg-success text-dark">
                                        <span
                                             v-if="tradeType(employee.is_new_joiner, employee.type) == 'Internet Target New Joiner'">
                                            {{ performanceDate.data.intenet_target_new_joiner  }}
                                        </span>
                                        <span
                                            v-if="tradeType(employee.is_new_joiner, employee.type) == 'Trade Target New Joiner'">
                                            {{ performanceDate.data.trade_target_new_joiner  }}
                                        </span>
                                        <span
                                            v-if="tradeType(!employee.is_new_joiner, employee.type) == 'Internet Target'">
                                            {{ performanceDate.data.internet_target  }}
                                        </span>
                                        <span
                                            v-if="tradeType(!employee.is_new_joiner, employee.type) == 'Trade Target'">
                                            {{ performanceDate.data.trade_target  }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            v-if="tradeType(employee.is_new_joiner, employee.type) == 'Internet Target New Joiner'">
                                            {{ performance.total_internet_target_new_joiner }}
                                        </span>
                                        <span
                                            v-if="tradeType(employee.is_new_joiner, employee.type) == 'Trade Target New Joiner'">
                                            {{ performance.total_trade_target_new_joiner }}
                                        </span>
                                        <span
                                            v-if="tradeType(!employee.is_new_joiner, employee.type) == 'Internet Target'">
                                            {{ performance.total_internet_target }}
                                        </span>
                                        <span
                                            v-if="tradeType(!employee.is_new_joiner, employee.type) == 'Trade Target'">
                                            {{ performance.total_trade_target }}
                                        </span>
                                    </td>
                                    <td></td>

                                </tr>
                                <tr>
                                    <td class="bg-primary">
                                        Process
                                    </td>
                                    <td
                                        v-for="(performanceDate, performanceDateKey) in performance.data"
                                        :key="performanceDateKey"
                                        class="bg-primary">
                                        {{ performanceDate.data.scanners_count }}
                                    </td>
                                    <td>
                                        {{ performance.total_scanners_count }}
                                    </td>
                                    <td>
                                        <span
                                            v-if="tradeType(employee.is_new_joiner, employee.type) == 'Internet Target New Joiner'">
                                            {{ performance.internet_new_joiner_percentage }}
                                        </span>
                                        <span
                                            v-if="tradeType(employee.is_new_joiner, employee.type) == 'Trade Target New Joiner'">
                                            {{ performance.trade_new_joiner_percentage }}
                                        </span>
                                        <span
                                            v-if="tradeType(!employee.is_new_joiner, employee.type) == 'Internet Target'">
                                            {{ performance.internet_target_percentage }}
                                        </span>
                                        <span
                                            v-if="tradeType(!employee.is_new_joiner, employee.type) == 'Trade Target'">
                                            {{ performance.trade_target_percentage }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-warning text-dark">
                                        Qc Tagged
                                    </td>
                                    <td
                                        v-for="(performanceDate, performanceDateKey) in performance.data"
                                        :key="performanceDateKey"
                                        class="bg-warning text-dark">
                                        {{ performanceDate.data.qc_count }}
                                    </td>
                                    <td>
                                        {{ performance.total_qc_tagged }}
                                    </td>
                                    <td>
                                        <span>
                                            {{ performance.total_qc_percentage }}%
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </el-card>

        <target-performance-filter
            :visible.sync="showFilterDialog"
            :filters.sync="filters"
            @close="showFilterDialog = false">
        </target-performance-filter>
    </div>
</template>

<style scoped>
    .card-body {
        min-height: 150px;
        min-width: 150px;
        margin-right: 5px;
    }
    .list-group  > .list-group-horizontal > .list-group-item {
        width: 150px !important;
    }
    td {
        width: 150px !important;
        white-space:nowrap;
    }
</style>
<script>
    import { mapActions, mapGetters } from 'vuex'
    export default {
        name: "TargetPerformance",

        data() {
            return {
                showFilterDialog: false,
                filters: {
                    employees: [],
                    dateRange: null,
                    isNewJoiner: false,
                    type: null
                }
            }
        },

        computed: {
            ...mapGetters('targetperformance', ['performances', 'dates', 'loading']),

            canExportData() {
                return this.performances.length != 0
            }
        },

        methods: {
            ...mapActions('targetperformance', ['exportTargetPerformance']),

            openFilter() {
                this.showFilterDialog = true
            },

            tradeType(isNewJoiner, type) {
                if (!isNewJoiner, type == 'trade') {
                    return 'Trade Target'
                }
                if (!isNewJoiner, type == 'internet') {
                    return 'Internet Trade Target'
                }
                if (isNewJoiner, type == 'trade') {
                    return 'Trade Target New Joiner'
                }
                if (isNewJoiner, type == 'internet') {
                    return 'Internet Target New Joiner'
                }

            },

            clickExport() {
                console.log(this.filters)
                this.exportTargetPerformance(this.filters)
                .then(() => {
                    this.$notify({
                        title: 'Success',
                        message: 'Your data is being exported. Please wait a while and check the Export page for your export',
                        type: 'success'
                    })
                })
            }
        }
    }
</script>
