<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        :before-close="closeForm"
        width="60%">
        <el-form
            ref="processForm"
            :model="processForm"
            :rules="rules"
            v-loading="loading">
            <div class="row">
                <div class="col-md-6">
                    <el-form-item
                        label="Name"
                        prop="name"
                        :error="hasError('name')">
                        <el-input
                            v-model="processForm.name"
                            clearable
                            placeholder="Roll Express Packing"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Barcode"
                        prop="barcode"
                        :error="hasError('barcode')">
                        <el-input
                            v-model="processForm.barcode"
                            clearable
                            placeholder="E10092"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Categories"
                        prop="process_categories"
                        :error="hasError('process_categories')">
                        <el-select
                            v-model="processForm.process_categories"
                            filterable
                            multiple
                            clearable
                            collapse-tags
                            class="w-100">
                            <el-option
                                v-for="category in processCategories"
                                :key="category.id"
                                :label="category.name | ucWords"
                                :value="category.id">
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item
                        label="Stop/Start Button Required"
                        prop="stop_start_button_required">
                        <el-switch v-model="processForm.stop_start_button_required"></el-switch>
                    </el-form-item>

                    <el-form-item
                        label="Trade Target"
                        prop="trade_target"
                        :error="hasError('trade_target')">
                        <el-input
                            v-model="processForm.trade_target"
                            type="number"
                            clearable
                            placeholder="200"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Internet Target"
                        prop="internet_target"
                        :error="hasError('internet_target')">
                        <el-input
                            v-model="processForm.internet_target"
                            type="number"
                            clearable
                            placeholder="200"
                            class="w-100">
                        </el-input>
                    </el-form-item>
                </div>

                <div class="col-md-6">
                    <el-form-item
                        label="Trade Target New Joiner"
                        prop="trade_target_new_joiner"
                        :error="hasError('trade_target_new_joiner')">
                        <el-input
                            v-model="processForm.trade_target_new_joiner"
                            type="number"
                            clearable
                            placeholder="200"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Internet Target New Joiner"
                        prop="internet_target_new_joiner"
                        :error="hasError('internet_target_new_joiner')">
                        <el-input
                            v-model="processForm.internet_target_new_joiner"
                            type="number"
                            clearable
                            placeholder="200"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Team Trade Target"
                        prop="team_trade_target"
                        :error="hasError('team_trade_target')">
                        <el-input
                            v-model="processForm.team_trade_target"
                            type="number"
                            clearable
                            :min="1"
                            placeholder="200"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Team Internet Target"
                        prop="team_internet_target"
                        :error="hasError('team_internet_target')">
                        <el-input
                            v-model="processForm.team_internet_target"
                            type="number"
                            clearable
                            :min="1"
                            placeholder="200"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Process Manufacturing Time"
                        prop="process_manufacturing_time"
                        :error="hasError('process_manufacturing_time')">
                        <el-input
                            v-model="processForm.process_manufacturing_time"
                            type="number"
                            clearable
                            placeholder="5"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Color"
                        prop="color"
                        :error="hasError('color')">
                        <el-color-picker v-model="processForm.color"></el-color-picker>
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
                @click="validate"
                type="primary"
                class="btn-primary">
		    	Save
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
    import {dialog} from "../../mixins/dialog"
    import {formHelper} from '../../mixins/formHelper'

    export default {
        name: "ProcessForm",

        mixins: [dialog, formHelper],

        props: {
            model: {},
            processCategories: {}
        },

        data() {
            return {
                loading: false,
                processForm: {},
                rules: {
                    team_trade_target: {required: true, message: 'Team trade target field is required.'},
                    team_internet_target: {required: true, message: 'Team internet target field is required.'},
                }
            }
        },

        computed: {
            hasModel() {
                return !!(this.model && this.model.id)
            },

            dialogTitle() {
                return this.hasModel ? 'Update Process' : 'Create New Process'
            },
        },

        created() {
            this.processForm = this.getDefaultFieldValues()
        },

        methods: {
            validate() {
                this.$refs.processForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()

                        if (this.hasModel) {
                            this.updateProcess()

                            return
                        }

                        this.createNewProcess()
                    }
                })
            },

            createNewProcess() {
                this.loading = true

                this.$API.Processes.store(this.processForm)
                    .then(res => {
                        if (res.data) {
                            this.$EventBus.fire('PROCESS_CREATE')

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

            updateProcess() {
                this.loading = true

                this.$API.Processes.update(this.processForm, this.processForm.id)
                    .then(res => {
                        if (res.data) {
                            this.$EventBus.fire('PROCESS_UPDATE')

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

                this.processForm = {
                    id: this.model.id,
                    name: this.model.name,
                    barcode: this.model.barcode,
                    trade_target: this.model.trade_target,
                    internet_target: this.model.internet_target,
                    trade_target_new_joiner: this.model.trade_target_new_joiner,
                    internet_target_new_joiner: this.model.internet_target_new_joiner,
                    process_manufacturing_time: this.model.process_manufacturing_time,
                    stop_start_button_required: this.model.stop_start_button_required,
                    team_trade_target: this.model.team_trade_target,
                    team_internet_target: this.model.team_internet_target,
                    process_categories: this.model.process_categories.map(pc => pc.id),
                    color: this.model.color
                }
            },

            getDefaultFieldValues() {
                return {
                    id: null,
                    name: null,
                    barcode: null,
                    process_target: null,
                    new_joiner_target: null,
                    process_manufacturing_time: null,
                    stop_start_button_required: false,
                    team_trade_target: null,
                    team_internet_target: null,
                    process_categories: [],
                    color: null
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
                this.processForm = this.getDefaultFieldValues()

                if (this.$refs.processForm) {
                    setTimeout(_ => {
                        this.$refs.processForm.clearValidate()
                    },200)
                }
            },
        },

        watch: {
            model(value) {
                if (value) {
                    this.initializeForm()
                } else {
                    this.processForm = this.getDefaultFieldValues()
                }
            }
        }
    }
</script>
