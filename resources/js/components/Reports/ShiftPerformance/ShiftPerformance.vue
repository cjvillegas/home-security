<template>
    <div>
        <global-page-header title="Shift Performance"></global-page-header>
        <el-card class="box-card mt-2" v-loading="loading">
            <div class="row" v-if="!shiftPerformanceView">
                <div class="col-md-4"></div>
                    <div class="col-md-5">
                        <el-form label-width="250px">
                            <el-form-item
                                label="Please select the Department: ">
                                <el-select
                                    v-model="form.selectedDepartments"
                                    placeholder="Departments"
                                    :multiple="isMultiple"
                                    :collapse-tags="isMultiple">
                                    <el-option
                                        v-for="(department, departmentKey) in departments.DEPARTMENTS"
                                        :label="department.value"
                                        :value="department.value"
                                        :key="departmentKey">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item
                                label="Please select the Shift: ">
                                <el-select
                                    v-model="form.selectedShifts"
                                    placeholder="Shifts"
                                    :multiple="isMultiple"
                                    :collapse-tags="isMultiple">
                                    <el-option
                                        v-for="shift in shifts"
                                        :key="shift.id"
                                        :value="shift.id"
                                        :label="shift.name | ucWords">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item
                                label="Please select the Date Range: ">
                                <el-date-picker
                                    v-model="form.dateRange"
                                    @change="datesChange"
                                    :picker-options="pickerOption"
                                    type="daterange"
                                    range-separator="~"
                                    start-placeholder="Start date"
                                    end-placeholder="End date">
                                </el-date-picker>
                            </el-form-item>
                            <el-form-item>
                                <el-button
                                    type="primary"
                                    @click="runReport">
                                    Run Report
                                </el-button>
                            </el-form-item>
                        </el-form>
                        <p>
                            Please note this report will include only the final processes and the number of blinds planned for the specific date.
                        </p>
                    </div>
                <div class="col-md-3"></div>
            </div>

            <shift-performance-view v-if="shiftPerformanceView">
            </shift-performance-view>
        </el-card>
    </div>
</template>

<script>
    import { mapActions, mapGetters, mapMutations } from 'vuex'
    import moment from "moment"
    import * as departments from '../../../constants/departments'
    export default {
        data() {
            let defaultProps = {
                date: moment().format('YYYY-MM-DD')
            }

            return {
                departments,
                form: {
                    selectedDepartments: [],
                    selectedShifts: [],
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
            isMultiple() {
                return true
            },

            ...mapGetters(['shifts']),
            ...mapGetters('shiftPerformance', ['shiftPerformanceView', 'loading'])
        },

        methods: {
            runReport() {
                if (this.form.selectedDepartments.length  == 0 || this.form.selectedShifts.length == 0 || this.form.dateRange == null) {
                    this.$notify.error({
                        title: 'Invalid Input',
                        message: "Please fill up all the required filters."
                    });
                    return
                }
                this.setForm(this.form)
                this.runShiftPerformanceReport(this.form)
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

            ...mapActions('shiftPerformance', ['runShiftPerformanceReport']),
            ...mapMutations('shiftPerformance', ['setForm'])
        }
    }
</script>
