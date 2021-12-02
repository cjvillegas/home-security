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
                                    @change="handleChange"
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
    import * as departments from '../../../constants/departments'
    export default {
        data() {
            return {
                departments,
                form: {
                    selectedDepartments: [],
                    selectedShifts: [],
                    dateRange: null
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
            handleChange() {

            },
            runReport() {
                this.setForm(this.form)
                this.runShiftPerformanceReport(this.form)
            },
            ...mapActions('shiftPerformance', ['runShiftPerformanceReport']),
            ...mapMutations('shiftPerformance', ['setForm'])
        }
    }
</script>
