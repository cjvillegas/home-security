<template>
    <div>
        <global-page-header title="QC Report"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search quality control tag..."
                        @keyup.enter.native.prevent="applySearch"
                        style="width: 250px">
                    </el-input>
                </div>

                <div class="ml-auto">
                    <global-filter-box>
                        <global-date-range-selector
                            :value.sync="filters.dateRange">
                        </global-date-range-selector>

                        <global-user-selector
                            class="mt-3"
                            :value.sync="filters.users"
                            :is-multiple="true">
                        </global-user-selector>

                        <global-employee-selector
                            class="mt-3"
                            :value.sync="filters.employees"
                            :is-multiple="true">
                        </global-employee-selector>

                        <global-process-selector
                            class="mt-3"
                            :value.sync="filters.processes"
                            :is-multiple="true">
                        </global-process-selector>

                        <global-quality-control-selector
                            class="mt-3"
                            :value.sync="filters.qualityControls"
                            :is-multiple="true">
                        </global-quality-control-selector>

                        <el-button
                            @click="getList"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>

                   <el-button
                       @click="exportQcFaultData"
                        type="success">
                       <i class="fas fa-file-export"></i> Export
                   </el-button>
                </div>
            </div>

            <el-table
                fit
                :data="qualityControls">
                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop"
                    :width="column.width ? column.width : ''">
                    <template slot-scope="scope">
                        <template v-if="['employee_full_name', 'user_name', 'process_name'].includes(column.prop)">
                            {{ scope.row[column.prop] | ucWords}}
                        </template>
                        <template v-else-if="['qc_fault_tag_at'].includes(column.prop)">
                            {{ scope.row[column.prop] | fixDateTimeByFormat('MMM DD, YYYY hh:mm a')}}
                        </template>
                        <template v-else>
                            {{ scope.row[column.prop] }}
                        </template>
                    </template>
                </el-table-column>
            </el-table>

            <div class="text-right">
                <el-pagination
                    class="mt-3"
                    background
                    layout="total, sizes, prev, pager, next"
                    :total="filters.total"
                    :page-size="filters.size"
                    :page-sizes="[10, 25, 50, 100]"
                    :current-page="filters.page"
                    @size-change="handleSize"
                    @current-change="handlePage">
                </el-pagination>
            </div>
        </el-card>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from "../../mixins/pagination";
    import GlobalQualityControlSelector from "../GlobalComponents/Filters/GlobalQualityControlSelector";

    export default {
        name: "QcReport",

        components: {GlobalQualityControlSelector},

        mixins: [pagination],

        data() {
            let columns = [
                {label: 'ID', prop: 'qc_fault_id', showOverflowTooltip: true, sortable: true, width: '80'},
                {label: 'Employee', prop: 'employee_full_name', showOverflowTooltip: true, sortable: true},
                {label: 'User', prop: 'user_name', showOverflowTooltip: true, sortable: true},
                {label: 'Quality Control', prop: 'quality_control_code', showOverflowTooltip: true, sortable: true},
                {label: 'Process', prop: 'process_name', showOverflowTooltip: true, sortable: true},
                {label: 'Blind ID', prop: 'scanner_blind_id', showOverflowTooltip: true, sortable: true},
                {label: 'Description', prop: 'qc_fault_description', showOverflowTooltip: false, sortable: false},
                {label: 'Operation Date', prop: 'qc_fault_operation_date', showOverflowTooltip: false, sortable: false},
                {label: 'Tag At', prop: 'qc_fault_tag_at', showOverflowTooltip: false, sortable: false},
            ]

            return {
                loading: false,
                filters: {
                    searchString: null,
                    users: [],
                    employees: [],
                    processes: [],
                    qualityControls: [],
                    dateRange: null
                },
                qualityControls: [],
                columns: columns
            }
        },

        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'

            this.getList()
        },

        methods: {
            applySearch() {
                this.filters.page = 1

                this.getList()
            },

            getList() {
                this.loading = true

                this.$API.Reports.getQcList(this.filters)
                .then(res => {
                    this.qualityControls = res.data.data
                    this.filters.total = res.data.total
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            exportQcFaultData() {
                let filters = cloneDeep(this.filters)
                delete filters.size
                delete filters.page

                this.loading = true

                this.$API.Reports.exportQcFaultData(filters)
                .then(res => {
                    if (res.data.success) {
                        this.$notify({
                            title: 'QC Fault Report',
                            message: res.data.message,
                            type: 'success'
                        })
                    }
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
