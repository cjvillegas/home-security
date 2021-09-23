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
                </div>

                <div class="col-md-6">
                    <el-form-item
                        label="Process Target"
                        prop="process_target"
                        :error="hasError('process_target')">
                        <el-input
                            v-model="processForm.process_target"
                            type="number"
                            clearable
                            placeholder="200"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="New Joiner Target"
                        prop="new_joiner_target"
                        :error="hasError('new_joiner_target')">
                        <el-input
                            v-model="processForm.new_joiner_target"
                            type="number"
                            clearable
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
                :disabled="hasModel"
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

                }
            }
        },

        computed: {
            hasModel() {
                return this.model && this.model.id
            },

            dialogTitle() {
                return this.hasModel ? 'Update Process' : 'Create New Process'
            },
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
                    process_target: this.model.process_target,
                    new_joiner_target: this.model.new_joiner_target,
                    process_manufacturing_time: this.model.process_manufacturing_time,
                    stop_start_button_required: this.model.stop_start_button_required,
                    process_categories: this.model.process_categories.map(pc => pc.id)
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
                    process_categories: []
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
                }
            }
        }
    }
</script>
