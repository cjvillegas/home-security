<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        :before-close="closeForm"
        width="60%">
        <el-form
            ref="employeeForm"
            :model="employeeForm"
            :rules="rules"
            v-loading="loading">
            <div class="row">
                <div class="col-md-6">
                    <el-form-item
                        label="Full Name"
                        prop="fullname"
                        :error="hasError('fullname')">
                        <el-input
                            v-model="employeeForm.fullname"
                            clearable
                            placeholder="Admin Josh"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Barcode"
                        prop="barcode"
                        :error="hasError('barcode')">
                        <el-input
                            v-model="employeeForm.barcode"
                            clearable
                            disabled
                            placeholder="This field will be auto-generated"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Pin Code"
                        prop="pin_code"
                        :error="hasError('pin_code')">
                        <el-input
                            v-model="employeeForm.pin_code"
                            clearable
                            placeholder="PIN1009"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Shift Target"
                        prop="target"
                        :error="hasError('target')">
                        <el-input-number
                            v-model="employeeForm.target"
                            clearable
                            placeholder="10"
                            class="w-100">
                        </el-input-number>
                    </el-form-item>

                    <el-form-item
                        v-if="hasModel"
                        label="Is Active"
                        prop="is_active">
                        <el-switch v-model="employeeForm.is_active"></el-switch>
                    </el-form-item>
                </div>
                <div class="col-md-6">
                    <el-form-item
                        label="Working Hours"
                        prop="standard_working_hours"
                        :error="hasError('standard_working_hours')">
                        <el-input
                            v-model="employeeForm.standard_working_hours"
                            type="number"
                            placeholder="7.8"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Clock No."
                        prop="clock_num"
                        :error="hasError('clock_num')">
                        <el-input
                            v-model="employeeForm.clock_num"
                            type="number"
                            placeholder="1997"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Shift"
                        prop="shift_id"
                        :error="hasError('shift_id')">
                        <el-select
                            v-model="employeeForm.shift_id"
                            filterable
                            clearable
                            placeholder="Select a shift"
                            class="w-100">
                            <el-option
                                v-for="shift in shifts"
                                :key="shift.id"
                                :label="shift.name | ucWords"
                                :value="shift.id">
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item
                        label="Team"
                        prop="team_id"
                        :error="hasError('team_id')">
                        <el-select
                            v-model="employeeForm.team_id"
                            filterable
                            clearable
                            placeholder="Select a team"
                            class="w-100">
                            <el-option
                                v-for="team in teams"
                                :key="team.id"
                                :label="team.name | ucWords"
                                :value="team.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <span
            slot="footer"
            class="dialog-footer">
		    <el-button
                @click="closeForm">
		    	Close
		    </el-button>
		    <el-button
                :disabled="hasModel && !hasFormChange"
                @click="validate"
                type="primary"
                class="btn-primary">
		    	Save
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
    import {dialog} from "../../mixins/dialog";
    import {formHelper} from "../../mixins/formHelper";
    export default {
        name: "EmployeeForm",
        mixins: [dialog, formHelper],
        props: {
            model: {},
            shifts: {
                required: true,
                type: Array
            },
            teams: {
                required: true,
                type: Array
            }
        },
        data() {
            return {
                employeeForm: this.getDefaultFieldValues(),
                rules: {
                    fullname: {required: true, message: 'Full Name field is required.', trigger: ['blur', 'change']},
                    pin_code: {required: true, message: 'Pin Code field is required', trigger: ['blur', 'change']},
                    standard_working_hours: {required: true, validator: this.validateWorkingHours, trigger: 'change'},
                    clock_num: {required: true, validator: this.validateClockNum, trigger: 'change'},
                    shift_id: {required: true, message: 'Shift field is required', trigger: 'change'},
                    team_id: {required: true, message: 'Team field is required', trigger: 'change'},
                },
                loading: false
            }
        },
        computed: {
            hasModel() {
                return this.model && this.model.id
            },
            dialogTitle() {
                return this.hasModel ? 'Update Employee' : 'Create New Employee'
            },
            hasFormChange() {
                for (let x in this.employeeForm) {
                    // if mode is create and the field is is_active, skip
                    if (!this.hasModel && x === 'is_active') {
                        continue
                    }

                    let form = this.employeeForm[x]
                    let value = this.hasModel ? this.model[x] : null

                    if ((this.hasModel && form !== value) || (!this.hasModel && !!form)) {
                        return true
                    }
                }

                return false
            }
        },
        methods: {
            validate() {
                this.$refs.employeeForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()

                        if (this.hasModel) {
                            this.updateEmployee()

                            return
                        }

                        this.createNewEmployee()
                    }
                })
            },
            createNewEmployee() {
                this.loading = true

                this.$API.Employee.store(this.employeeForm)
                    .then(res => {
                        if (res.data) {
                            this.$EventBus.fire('EMPLOYEE_CREATE')

                            setTimeout(_ => {
                                this.closeForm(true)
                            }, 300)
                        }
                    })
                    .catch(err => {
                        console.log(err)

                        if (err.response.status === 422) {
                            this.setErrors(err.response.data.errors)
                        }
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            updateEmployee() {
                this.loading = true

                this.$API.Employee.update(this.employeeForm, this.employeeForm.id)
                    .then(res => {
                        if (res.data) {
                            this.$EventBus.fire('EMPLOYEE_UPDATE')

                            setTimeout(_ => {
                                this.closeForm(true)
                            }, 300)
                        }
                    })
                    .catch(err => {
                        console.log(err)

                        if (err.response.status === 422) {
                            this.setErrors(err.response.data.errors)
                        }
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            initializeForm() {
                if (!this.model || !this.model.id) {
                    return
                }

                this.employeeForm = {
                    id: this.model.id,
                    fullname: this.model.fullname,
                    barcode: this.model.barcode,
                    pin_code: this.model.pin_code,
                    target: this.model.target,
                    standard_working_hours: this.model.standard_working_hours,
                    clock_num: this.model.clock_num,
                    shift_id: this.model.shift_id,
                    team_id: this.model.team_id,
                    is_active: this.model.is_active,
                }
            },
            closeForm(ignoreChecker = false) {
                if (!ignoreChecker && this.hasFormChange) {
                    this.$confirm(`Are you sure you want to close this form?`, 'Confirm', {
                        confirmButtonText: "Yes, I'm Sure",
                        cancelButtonText: 'No, Not Sure',
                        type: 'warning'
                    })
                        .then(_ => {
                            this.resetForm()
                            this.closeModal()
                        })
                        .catch(_ => {})

                    return
                }

                this.resetForm()
                this.closeModal()
            },
            resetForm() {
                this.employeeForm = this.getDefaultFieldValues()

                if (this.$refs.employeeForm) {
                    setTimeout(_ => {
                        this.$refs.employeeForm.clearValidate()
                    },200)
                }
            },
            getDefaultFieldValues() {
                return {
                    id: null,
                    fullname: null,
                    barcode: null,
                    pin_code: null,
                    target: null,
                    standard_working_hours: null,
                    clock_num: null,
                    shift_id: null,
                    team_id: null,
                    is_active: true,
                }
            },
            validateWorkingHours(rule, value, callback) {
                if (!value || value <= 0) {
                    callback(new Error('Please specify a valid Working Hours'))
                }

                callback()
            },
            validateClockNum(rule, value, callback) {
                if (value !== undefined && value !== null) {
                    let toStr = value.toString()
                    if (toStr.length < 4) {
                        callback(new Error('Please input at least 4 characters.'))

                    }

                    callback()
                }

                callback(new Error('Please specify a valid Clock No.'))
            }
        },
        watch: {
            model(value) {
                if (value) {
                    this.initializeForm()

                }
            }
        }
    }
</script>
