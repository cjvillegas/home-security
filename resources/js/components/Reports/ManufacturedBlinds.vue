<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Manufactured Blinds</h4>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div class="ml-auto">
                    <global-filter-box>
                        <div>
                            <label for="date">Select Dates</label>
                            <el-date-picker
                                v-model="filters.dateRange"
                                @change="datesChange"
                                :picker-options="pickerOption"
                                range-separator="~"
                                start-placeholder="Start date"
                                end-placeholder="End date"
                                :clearable="false"
                                type="daterange"
                                class="w-100">
                            </el-date-picker>
                        </div>

                        <el-button
                            @click="fetchBlinds"
                            :disabled="disableApplyFilterButton"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>
                    <el-button
                        @click="clickExportData"
                        :disabled="!canExportData"
                        type="success">
                        <i class="fas fa-file-export"></i> Export
                    </el-button>
                </div>
            </div>
            <div v-if="filters.dateRange">
                <p class="text-muted">Selected Dates: {{ dateVisual }}</p>
            </div>
            <el-table
                :data="blinds"
                :summary-method="getOverallTotal"
                show-summary>
                <el-table-column
                    label="Date"
                    sortable>
                    <template slot-scope="scope">
                         {{ scope.row.date }}
                    </template>
                </el-table-column>

                <el-table-column
                    prop="shift"
                    label="Shift">
                </el-table-column>

                <el-table-column
                    prop="manufactured_blinds"
                    label="Manufactured Blinds">
                </el-table-column>

                <el-table-column
                    prop="invoiced_blinds"
                    label="Invoiced Blinds">
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep"
    import { mapActions, mapGetters } from 'vuex'
    import moment from "moment";
    export default {
        data() {
            let defaultProps = {
                date: moment().format('YYYY-MM-DD')
            }

            return {
                filters: {
                    dateRange: null
                },
                onPick: [],
                pickerOption: {
                    disabledDate: time => {
                        if (!this.onPick || !this.onPick.length) {
                            return false
                        }

                        let momentTime = moment(this.onPick[0])
                        let momentNow = moment(time)

                        /**
                         * prevent selection of dates that will be more than 31 days
                         * this logic is to prevent stack overflow error in our server when users
                         * want to export loads of data.
                         */
                        return Math.abs(momentTime.diff(momentNow, 'days')) > 31
                    },
                    onPick: ({minDate, maxDate}) => {
                        this.onPick[0] = minDate
                        this.onPick[1] = maxDate
                    }
                }
            }
        },

        computed: {
            ...mapGetters('manufacturedblind', ['blinds', 'loading']),

            canExportData() {
                return this.blinds.length > 0
            },

            disableApplyFilterButton() {
                return !this.filters.dateRange
            },

            dateVisual() {
                let sod = moment(this.filters.dateRange[0]).format('MMM DD, YYYY')
                let eod = moment(this.filters.dateRange[1]).format('MMM DD, YYYY')
                return  sod + ' - ' + eod
            }
        },

        methods: {
            ...mapActions('manufacturedblind', ['getBlinds', 'exportManufacturedBlinds']),

            fetchBlinds() {
                let {sod, eod} = this.getSodAndEod()
                sod = sod.format('YYYY-MM-DD HH:mm')
                eod = eod.format('YYYY-MM-DD HH:mm')

                let parameters = cloneDeep(this.filters)
                parameters.start = sod
                parameters.end = eod

                this.getBlinds(parameters)
            },

            clickExportData() {
                let {sod, eod} = this.getSodAndEod()
                sod = sod.format('YYYY-MM-DD HH:mm')
                eod = eod.format('YYYY-MM-DD HH:mm')

                let parameters = cloneDeep(this.filters)
                parameters.start = sod
                parameters.end = eod

                this.exportManufacturedBlinds(parameters)
                .then(() => {
                    this.$notify({
                        title: 'Success',
                        message: 'Your data is being exported. Please wait a while and check the Export page for your export',
                        type: 'success'
                    })
                })
            },

            getOverallTotal(param) {
                const { columns, data } = param;
                const sums = [];
                columns.forEach((column, index) => {
                        if (index === 0) {
                            sums[index] = 'Overall Total';
                            return;
                        }
                        const values = data.map(item => Number(item[column.property]));
                        if (!values.every(value => isNaN(value))) {
                            sums[index] = values.reduce((prev, curr) => {
                            const value = Number(curr);
                            if (!isNaN(value)) {
                                return prev + curr;
                            } else {
                                return prev;
                            }
                            }, 0);
                        } else {
                            sums[index] = 'N/A';
                        }
                    }
                );

                return sums;
            },

            getSodAndEod() {
                // define the SOD and EOD
                let sod = moment(this.filters.dateRange[0]).hour(6).startOf('hour')
                let eod = moment(this.filters.dateRange[1]).add(1, 'day').hour(6).startOf('hour')

                return {sod, eod}
            },

            datesChange() {
                if (this.selectedFilters.date && this.selectedFilters.date.length) {
                    let [start, end] = this.selectedFilters.date
                    if (Math.abs(moment(end).diff(moment(start), 'days')) > 31) {
                        this.selectedFilters.date = []

                        this.$notify.error({
                            title: 'Invalid Input',
                            message: "You can't select dates more than 31 days. If you have any concerns please report this to your administrator."
                        });
                    }

                }
            },
        }
    }
</script>
