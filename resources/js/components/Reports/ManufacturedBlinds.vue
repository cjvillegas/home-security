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
                            @click="getBlinds(filters)"
                            :disabled="disableApplyFilterButton"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>
                    <el-button
                        @click="exportManufacturedBlinds"
                        :disabled="!canExportData"
                        type="success">
                        <i class="fas fa-file-export"></i> Export
                    </el-button>
                </div>
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
            ...mapGetters('manufacturedblinds', ['blinds', 'loading']),

            canExportData() {
                return false
            },
            disableApplyFilterButton() {
                return false
            },
        },

        methods: {
            ...mapActions('manufacturedblinds', ['getBlinds', 'exportManufacturedBlinds']),

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
