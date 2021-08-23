<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        @close="closeForm"
        width="50%">
        <el-form
            :model="processSequenceForm"
            :rules="rules"
            v-loading="loading"
            ref="processSequenceForm">
            <div class="row">
                <div class="col md-6">
                    <el-form-item
                        label="Name"
                        prop="name"
                        :error="hasError('name')">
                        <el-input
                            v-model="processSequenceForm.name"
                            :disabled="this.mode === 'view'"
                            clearable
                            placeholder="Roller Sequence"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Process Target"
                        prop="process_target"
                        :error="hasError('process_target')">
                        <el-input
                            v-model="processSequenceForm.process_target"
                            type="number"
                            :disabled="this.mode === 'view'"
                            clearable
                            placeholder="200"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Stop/Start Button Required"
                        prop="stop_start_button_required">
                        <el-switch v-model="processSequenceForm.stop_start_button_required"></el-switch>
                    </el-form-item>
                </div>

                <div class="col md-6">
                    <el-form-item
                        label="New Joiner Target"
                        prop="new_joiner_target"
                        :error="hasError('new_joiner_target')">
                        <el-input
                            v-model="processSequenceForm.new_joiner_target"
                            type="number"
                            :disabled="this.mode === 'view'"
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
                            v-model="processSequenceForm.process_manufacturing_time"
                            type="number"
                            :disabled="this.mode === 'view'"
                            clearable
                            placeholder="5"
                            class="w-100">
                        </el-input>
                    </el-form-item>
                </div>
            </div>

            <el-input
                hidden
                class="w-100">
            </el-input>
        </el-form>

        <span
            v-if="this.mode !== 'view'"
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
    import cloneDeep from 'lodash/cloneDeep'
    import {dialog} from "../../mixins/dialog";
    import {formHelper} from "../../mixins/formHelper";

    export default {
        name: "ProcessSequenceForm",
        mixins: [dialog, formHelper],
        props: {
            model: {},
            mode: {
                type: String,
                default: 'create'
            }
        },
        data() {
            return {
                processSequenceForm: {
                    id: null,
                    name: null,
                    process_target: null,
                    new_joiner_target: null,
                    process_manufacturing_time: null,
                    stop_start_button_required: true,
                },
                rules: {
                    name: {required: true, message: 'Name is required', trigger: 'blur'}
                },
                loading: false
            }
        },
        methods: {
            validate() {
                this.$refs.processSequenceForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()

                        if (this.hasModel) {
                            this.updateProcessSequence()

                            return
                        }

                        this.createNewProcessSequence()
                    }
                })
            },
            createNewProcessSequence() {
                this.loading = true

                let postData = cloneDeep(this.processSequenceForm)

                this.$API.ProcessSequence.store(postData)
                    .then(res => {
                        this.$EventBus.fire('SETTINGS_PROCESS_SEQUENCES_CREATE')
                        setTimeout(_ => {
                            this.closeForm()
                        }, 300)
                    })
                    .catch(err => {
                        if (err.response.status === 422) {
                            this.setErrors(err.response.data.errors)
                        }
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            updateProcessSequence() {
                this.loading = true

                let postData = cloneDeep(this.processSequenceForm)

                this.$API.ProcessSequence.update(postData, postData.id)
                    .then(res => {
                        this.$EventBus.fire('SETTINGS_PROCESS_SEQUENCES_UPDATE')

                        setTimeout(_ => {
                            this.closeForm()
                        }, 300)
                    })
                    .catch(err => {
                        if (err.response.status === 422) {
                            this.setErrors(err.response.data.errors)
                        }
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            closeForm() {
                this.resetForm()

                this.closeModal()
            },
            resetForm() {
                this.processSequenceForm = {
                    id: null,
                    name: null,
                    process_target: null,
                    new_joiner_target: null,
                    process_manufacturing_time: null,
                    stop_start_button_required: true,
                }
            },
            populateForm() {
                this.processSequenceForm = {
                    id: this.model.id,
                    name: this.model.name,
                    process_target: this.model.process_target,
                    new_joiner_target: this.model.new_joiner_target,
                    process_manufacturing_time: this.model.process_manufacturing_time,
                    stop_start_button_required: this.model.stop_start_button_required,
                }
            }
        },
        computed: {
            hasModel() {
                return this.model && this.model.id
            },
            dialogTitle() {
                return this.model && this.model.id ? 'Update Process Sequence' : 'Create New Process Sequence'
            }
        },
        watch: {
            model: {
                handler() {
                    if (this.hasModel) {
                        this.populateForm()
                    }
                },
                deep: true,
                immediate: true
            },
            visible() {
                if (this.$refs.processSequenceForm) {
                    this.$refs.processSequenceForm.clearValidate()
                }
            }
        }
    }
</script>
