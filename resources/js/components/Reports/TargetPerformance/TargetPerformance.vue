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
                </div>
            </div>
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-10">
                    <ul class="list-group list-group-horizontal">
                        <li class="list-group-item mt-2 mr-2"
                            v-for="(date, dateKey) in dates"
                            :key="dateKey">
                            <h3>{{ date| fixDateByFormat('MMM DD, YYYY') }}</h3>
                        </li>
                    </ul>
                </div>
            </div>
            <el-collapse>
                <el-collapse-item
                    v-for="(employee, employeeKey) in performances"
                    :key="employeeKey"
                    :title="employee.employee_name">
                    <div class="row"
                        v-for="(performance, performanceKey) in employee.performances"
                        :key="performanceKey">
                        <div class="col-md-2">
                            {{ performance.process_name }}
                        </div>
                        <div class="col-md-10">
                            <ul class="list-group list-group-horizontal">
                                <li class="list-group-item mt-2 mr-2"
                                    v-for="(performanceDate, performanceDateKey) in performance.data"
                                    :key="performanceDateKey">
                                    <span> QC Tagged : {{ performanceDate.data.qc_count }} </span>
                                    <span> Scanners Count:   {{ performanceDate.data.scanners_count }} </span>
                                    <span v-if="employee.is_new_joiner && employee.type=='internet'">
                                        Internet Target New Joiner: {{ performanceDate.data.intenet_target_new_joiner  }}
                                    </span>
                                    <span v-if="employee.is_new_joiner && employee.type=='trade'">
                                        Trade Target New Joiner: {{ performanceDate.data.trade_target_new_joiner  }}
                                    </span>
                                    <span v-if="!employee.is_new_joiner && employee.type=='internet'">
                                        Internet Target: {{ performanceDate.data.intenet_target  }}
                                    </span>
                                    <span v-if="!employee.is_new_joiner && employee.type=='trade'">
                                        Trade Target: {{ performanceDate.data.trade_target  }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                    </div>
                </el-collapse-item>
            </el-collapse>

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
</style>
<script>
    import { mapGetters } from 'vuex'
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

            hasPerformancesData() {
                return this.performances.length != 0
            }
        },

        methods: {
            openFilter() {
                this.showFilterDialog = true
            }
        }
    }
</script>
