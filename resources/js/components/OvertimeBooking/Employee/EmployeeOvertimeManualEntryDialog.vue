<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Overtime Manual Entry"
        :before-close="closeForm">
        <el-form
            ref="employeeOvertimeForm"
            :model="employeeOvertimeForm"
            :rules="rules"
            v-loading="loading">
            <el-form-item
                label="Employee"
                prop="employee_id"
                :error="hasError('employee_id')">
                <el-select
                    v-model="employeeOvertimeForm.employee_id"
                    filterable
                    clearable
                    placeholder="Select Employee"
                    class="w-100">
                    <el-option
                        v-for="(employee, employeeKey) in employees"
                        :key="employeeKey"
                        :label="employee.fullname"
                        :value="employee.id">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item
                label="Date"
                prop="overtime_booking_id"
                :error="hasError('overtime_booking_id')">
                <el-select
                    v-model="employeeOvertimeForm.overtime_booking_id"
                    filterable
                    clearable
                    placeholder="Select Date"
                    class="w-100">
                    <el-option
                        v-for="(availableSlot, availableSlotKey) in availableSlots"
                        :key="availableSlotKey"
                        :label="dateDisplay(availableSlot.available_date, availableSlot.working_hours)"
                        :value="availableSlot.id">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item
                label="Department"
                prop="department"
                :error="hasError('department')">
                <el-select
                    v-model="employeeOvertimeForm.department"
                    filterable
                    clearable
                    placeholder="Select Department"
                    class="w-100">
                    <el-option
                        v-for="(department, departmentKey) in departments"
                        :key="departmentKey"
                        :label="department.label"
                        :value="department.value">
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item
                label="Shift"
                prop="shift"
                :error="hasError('shift')">
                <el-select
                    v-model="employeeOvertimeForm.shift"
                    filterable
                    clearable
                    placeholder="Select Shift"
                    class="w-100">
                    <el-option
                        v-for="(shift, shiftKey) in shifts"
                        :key="shiftKey"
                        :label="shift.label "
                        :value="shift.value">
                    </el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <span
            slot="footer"
            class="dialog-footer">
            <el-button
                @click="closeForm">
                Close
            </el-button>
            <el-button
                @click="validate"
                type="primary"
                class="btn-primary">
                Save
            </el-button>
        </span>
    </el-dialog>
</template>

<script>
    import moment from 'moment'
    import { mapActions, mapGetters } from 'vuex'
    import {dialog} from "../../../mixins/dialog"
    import {formHelper} from "../../../mixins/formHelper"
    export default {
        name: "EmployeeOvertimeManualEntryDialog",
        mixins: [dialog, formHelper],

        data() {
            return {
                employeeOvertimeForm: this.getDefaultFieldValues(),
                rules: {
                    employee_id: {required: true, message: 'Employee field is required.', trigger: ['blur', 'change']},
                    overtime_booking_id: {required: true, message: 'Working Date field is required.', trigger: ['blur', 'change']},
                    department: {required: true, message: 'Department field is required.', trigger: ['blur', 'change']},
                    shift: {required: true, message: 'Shift field is required.', trigger: ['blur', 'change']},
                },

                departments: [
                    {
                        label: 'Roller',
                        value: 'roller'
                    },
                    {
                        label: 'Venetian',
                        value: 'venetian'
                    },
                    {
                        label: 'Vertical',
                        value: 'vertical'
                    },
                    {
                        label: 'Roller Express',
                        value: 'roller express'
                    },
                    {
                        label: 'Despatch',
                        value: 'despatch'
                    }
                ],
                shifts: [
                    {
                        label: 'Morning',
                        value: 'morning'
                    },
                    {
                        label: 'Afternoon',
                        value: 'afternoon'
                    }
                ]
            }
        },

        created() {
            this.getAllSlots()
        },

        computed: {
            ...mapGetters('overtimeBooking', ['loading', 'availableSlots']),
            ...mapGetters(['employees'])
        },

        methods: {
            validate() {
                this.$refs.employeeOvertimeForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()

                        console.log(this.employeeOvertimeForm)
                        this.saveEmployeeOvertime(this.employeeOvertimeForm)
                        .then((res) => {
                            this.$notify({
                                title: 'Success',
                                message: res.data.message,
                                type: 'success'
                            })
                            setTimeout(_ => {
                                this.closeForm()
                            },200)
                        })
                    }
                })
            },

            closeForm() {
                this.resetForm()
                this.closeModal()
            },

            resetForm() {
                this.employeeOvertimeForm = this.getDefaultFieldValues()

                if (this.$refs.employeeOvertimeForm) {
                    setTimeout(_ => {
                        this.$refs.employeeOvertimeForm.clearValidate()
                    },200)
                }
            },

            getDefaultFieldValues() {
                return {
                    employee_id: null,
                    overtime_booking_id: null,
                    department: null,
                    shift: null
                }
            },

            dateDisplay(date, workingHours) {
                return `${moment(date).format('ll')} - ${workingHours} Working Hours`
            },

            ...mapActions('overtimeBooking', ['getAllSlots', 'saveEmployeeOvertime'])
        }
    }
</script>

<style>

</style>
