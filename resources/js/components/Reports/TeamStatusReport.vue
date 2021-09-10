<template>
    <div>
        <global-page-header title="Planned Work"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div class="ml-auto">
                    <global-filter-box>
                        <label>Select Date</label>
                        <el-date-picker
                            v-model="filters.date"
                            type="date"
                            clearable
                            placeholder="Pick a day"
                            class="w-100">
                        </el-date-picker>

                        <global-shift-selector
                            class="mt-3"
                            :value.sync="filters.shifts"
                            is-multiple>
                        </global-shift-selector>

                        <global-team-selector
                            class="mt-3"
                            :value.sync="filters.teams"
                            is-multiple>
                        </global-team-selector>

                        <el-button
                            @click="getList"
                            :disabled="disableApplyFilterButton"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>

                    <el-button
                        @click="exportTeamStatusReport"
                        :disabled="!canExportData"
                        type="success">
                        <i class="fas fa-file-export"></i> Export
                    </el-button>
                </div>
            </div>

            <el-table
                fit
                :data="teamStatuses">
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                    </el-empty>
                </template>

                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop"
                    :width="column.width ? column.width : ''">
                    <template
                        v-if="!!teamStatuses.length"
                        slot-scope="scope">
                        {{ scope.row[column.prop] }}
                    </template>
                        <!--                    <template slot-scope="scope">-->
<!--                        <template v-if="['employee_full_name', 'user_name', 'process_name'].includes(column.prop)">-->
<!--                            {{ scope.row[column.prop] | ucWords}}-->
<!--                        </template>-->
<!--                        <template v-else-if="['qc_fault_tag_at'].includes(column.prop)">-->
<!--                            {{ scope.row[column.prop] | fixDateByFormat('MMM DD, YYYY hh:mm a')}}-->
<!--                        </template>-->
<!--                        <template v-else>-->
<!--                            {{ scope.row[column.prop] }}-->
<!--                        </template>-->
<!--                    </template>-->
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
    import pagination from "../../mixins/pagination"
    import {mapGetters} from "vuex";

    export default {
        name: "TeamStatusReport",

        mixins: [pagination],

        data() {
            let columns = [
                {label: 'Folder', prop: 'folder_name', showOverflowTooltip: true, sortable: true},
                {label: 'Planned Blinds', prop: 'planned_blinds', showOverflowTooltip: true, sortable: true},
                {label: 'Not Started', prop: 'not_started', showOverflowTooltip: true, sortable: true},
                {label: 'Started Blinds', prop: 'started_blinds', showOverflowTooltip: true, sortable: true},
                {label: 'Completed Blinds', prop: 'completed_blinds', showOverflowTooltip: true, sortable: true},
                {label: 'Packed Blinds', prop: 'packed_blinds', showOverflowTooltip: true, sortable: true},
            ]

            return {
                loading: false,
                columns: columns,
                teamStatuses: [],
                filters: {
                    date: null,
                    teams: [],
                    shifts: []
                }
            }
        },

        computed: {
            ...mapGetters(['teams', 'shifts']),

            disableApplyFilterButton() {
                return !this.filters.date || !this.filters.shifts.length
            },

            canExportData() {
                return this.teamStatuses.length > 0
            }
        },

        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'
        },

        methods: {
            getList() {
                this.loading = true

                let filters = cloneDeep(this.filters)
                delete filters.shifts
                delete filters.teams
                filters.folders = this.buildFolders()

                this.$API.Reports.getTeamStatusList(filters)
                    .then(res => {
                        this.teamStatuses = res.data.data
                        this.filters.total = res.data.total
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            exportTeamStatusReport() {
                let filters = cloneDeep(this.filters)
                delete filters.size
                delete filters.page
                delete filters.shifts
                delete filters.teams
                filters.folders = this.buildFolders()

                this.loading = true

                this.$API.Reports.exportTeamStatusReport(filters)
                    .then(res => {
                        if (res.data.success) {
                            this.$notify({
                                title: 'Team Status Report',
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
            },

            buildFolders() {
                let folders = []

                for (let x of this.filters.shifts) {
                    let shift = this.shifts.find(s => s.id === x)

                    if (shift) {
                        if (!!this.filters.teams.length) {
                            for (let teamId of this.filters.teams) {
                                let folder = shift.name
                                let team = this.teams.find(t => t.id === teamId)

                                if (team) {
                                    folder += ' ' + team.name
                                }

                                folders.push(folder)
                            }
                        } else {
                            folders.push(shift.name)
                        }
                    }
                }

                return folders
            }
        }
    }
</script>
