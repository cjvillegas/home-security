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

            <!-- <div v-if="hasPerformancesData">
                <div class="row" v-for="(employee, employeeKey) in performances" :key="employeeKey">
                    <div class="col-md-12">
                        <h3>{{ employee.employee_name }}</h3>
                        <el-table
                            :data="employee.performances">
                            <el-table-column
                                prop="name"
                                label="Process Name">
                            </el-table-column>
                            <el-table-column
                                prop="scanners_count"
                                label="Scanners Count">
                            </el-table-column>
                            <el-table-column
                                prop="qc_count"
                                label="QC Tagged">
                            </el-table-column>
                            <el-table-column
                                v-if="filters.isNewJoiner && filters.type=='trade'"
                                prop="trade_target_new_joiner"
                                label="Trade Target">
                            </el-table-column>
                            <el-table-column
                                v-if="filters.isNewJoiner && filters.type=='internet'"
                                prop="internet_target_new_joiner"
                                label="Internet Target">
                            </el-table-column>
                            <el-table-column
                                v-if="!filters.isNewJoiner && filters.type=='trade'"
                                prop="trade_target"
                                label="Trade Target">
                            </el-table-column>
                            <el-table-column
                                v-if="!filters.isNewJoiner&& filters.type=='internet'"
                                prop="internet_target"
                                label="Internet Target">
                            </el-table-column>
                            <el-table-column
                                prop="date"
                                label="Date">
                            </el-table-column>
                        </el-table>
                    </div>
                </div>
            </div>

            <div v-else>
                <el-empty
                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                </el-empty>
            </div> -->
            <div
                class="row"
                v-for="(employee, employeeKey) in performances"
                :key="employeeKey">
                <h2 class="font-weight-light">
                    {{ employee.employee_name }}
                </h2>
                <hr>

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
                                <li class="list-group-item m-2"
                                    v-for="(performanceDate, performanceDateKey) in performance.data"
                                    :key="performanceDateKey">
                                    <span> QC Tagged : {{ performanceDate.qc_count }} </span>
                                    <span> Date: {{ performanceDate.date }} </span>
                                    <span> Scanners Count:   {{ performanceDate.scanners_count }} </span>
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
            ...mapGetters('targetperformance', ['performances', 'loading']),

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
