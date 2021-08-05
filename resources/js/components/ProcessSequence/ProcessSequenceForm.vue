<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        @close="closeForm"
        width="30%">
        <el-form
            :model="processSequenceForm"
            :rules="rules"
            v-loading="loading"
            ref="processSequenceForm">
            <el-form-item
                label="Name"
                prop="name"
                :error="hasError('name')">
                <el-input
                    @keyup.enter.prevent.native="validate"
                    v-model="processSequenceForm.name"
                    :disabled="this.mode === 'view'"
                    clearable
                    placeholder="Roller Sequence"
                    class="w-100">
                </el-input>
            </el-form-item>

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
                    name: null
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
                    name: null
                }
            },
            populateForm() {
                this.processSequenceForm = {
                    id: this.model.id,
                    name: this.model.name,
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
