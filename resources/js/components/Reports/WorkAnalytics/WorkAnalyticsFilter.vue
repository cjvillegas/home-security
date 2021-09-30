<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Work Analytics Filter"
        :before-close="closeModal">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <label for="shift">Shift</label>
                    <el-select
                        id="shift"
                        v-model="selectedFilters.shift"
                        filterable
                        placeholder="Select shift to show"
                        class="w-100">
                        <el-option label="Select All" :value="null"></el-option>
                        <el-option
                            v-for="shift in shifts"
                            :key="shift.id"
                            :label="shift.name"
                            :value="shift.id">
                        </el-option>
                    </el-select>
                </div>

                <div class="mt-3">
                    <global-process-selector
                        :value.sync="selectedFilters.processes"
                        property="barcode"
                        :is-multiple="true">
                    </global-process-selector>
                </div>
            </div>

            <div class="col-md-6">
                <div>
                    <global-employee-selector
                        :value.sync="selectedFilters.employees"
                        property="barcode"
                        :is-multiple="true">
                    </global-employee-selector>
                </div>

                <div class="mt-3">
                    <template v-if="!isDateRange">
                        <label for="date">Date</label>
                        <el-date-picker
                            v-model="selectedFilters.date"
                            :clearable="false"
                            :picker-options="{
                                disabledDate(time) {
                                    return time.getTime() > Date.now() || time.getDay() === 0
                                }
                            }"
                            type="date"
                            placeholder="Pick a day"
                            class="w-100">
                        </el-date-picker>
                    </template>

                    <template v-else>
                        <label for="date">Dates</label>
                        <el-date-picker
                            v-model="selectedFilters.date"
                            @change="datesChange"
                            :picker-options="pickerOption"
                            :clearable="false"
                            type="daterange"
                            placeholder="Pick a day"
                            class="w-100">
                        </el-date-picker>
                    </template>
                </div>
            </div>
        </div>

        <span
            slot="footer"
            class="dialog-footer">
		    <el-button
                @click="closeFilter">
		    	Close
		    </el-button>
		    <el-button
                @click="applyFilter"
                :disabled="!filterChanged"
                type="primary"
                class="btn-primary">
		    	Apply Filter
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import { dialog } from "../../../mixins/dialog";
    import { mapGetters } from "vuex";
    import moment from "moment";

    export default {
        name: "WorkAnalyticsFilter",

        mixins: [dialog],

        props: {
            filters: {
                type: Object,
                required: true
            },

            isDateRange: {
                default: false
            }
        },

        data() {
            let defaultProps = {
                shift: null,
                processes: [null],
                employees: [null],
                legend: 'process',
                date: moment().format('YYYY-MM-DD')
            }

            return {
                selectedFilters: defaultProps,
                tracker: defaultProps,
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
            ...mapGetters(['shifts']),

            filterChanged() {
                return JSON.stringify(this.selectedFilters) !== JSON.stringify(this.tracker)
            }
        },

        methods: {
            applyFilter() {
                this.$emit('update:filters', cloneDeep(this.selectedFilters))

                setTimeout(_ => {
                    this.$emit('filtersUpdated')

                    this.closeModal()

                    this.tracker = cloneDeep(this.selectedFilters)
                },100)
            },

            closeFilter() {
                this.selectedFilters = cloneDeep(this.tracker)
                this.closeModal()
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
        },

        watch: {
            filters: {
                handler() {
                    this.selectedFilters = cloneDeep(this.filters)
                    this.tracker = cloneDeep(this.selectedFilters)
                },
                deep: true,
                immediate: true
            }
        }
    }
</script>
