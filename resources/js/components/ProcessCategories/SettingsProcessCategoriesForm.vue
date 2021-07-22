<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        @close="closeForm"
        width="40%">
        <el-form
            :model="processCategoryForm"
            :rules="rules"
            ref="processCategoryForm">
            <el-form-item
                label="Code"
                prop="code"
                :error="hasError('code')">
                <el-input
                    v-model="processCategoryForm.code"
                    :disabled="this.mode === 'view'"
                    clearable
                    placeholder="CODE-1997"
                    class="w-100">
                </el-input>
            </el-form-item>

            <el-form-item
                label="Name"
                prop="name"
                :error="hasError('name')">
                <el-input
                    v-model="processCategoryForm.name"
                    :disabled="this.mode === 'view'"
                    clearable
                    placeholder="Roller"
                    class="w-100">
                </el-input>
            </el-form-item>
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
    import { dialog } from '../../mixins/dialog'
    import { formHelper } from '../../mixins/formHelper'

    export default {
        name: "SettingsProcessCategoriesForm",
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
                processCategoryForm: {
                    id: null,
                    code: null,
                    name: null
                },
                rules: {
                    code: {required: true, message: 'Code is required', trigger: 'blur'},
                    name: {required: true, message: 'Name is required', trigger: 'blur'},
                },
                loading: false
            }
        },
        methods: {
            validate() {
                this.$refs.processCategoryForm.validate(valid => {
                    if (valid) {
                        if (this.hasModel) {
                            this.updateProcessCategory()

                            return
                        }

                        this.createNewProcessCategory()
                    }
                })
            },
            createNewProcessCategory() {
                this.loading = true

                let postData = cloneDeep(this.processCategoryForm)

                this.$API.ProcessCategory.store(postData)
                    .then(res => {
                        this.$EventBus.fire('SETTINGS_PROCESS_CATEGORIES_CREATE')
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
            updateProcessCategory() {
                this.loading = true

                let postData = cloneDeep(this.processCategoryForm)

                this.$API.ProcessCategory.update(postData, postData.id)
                    .then(res => {
                        this.$EventBus.fire('SETTINGS_PROCESS_CATEGORIES_UPDATE')

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
                this.processCategoryForm = {
                    id: null,
                    code: null,
                    name: null
                }
            },
            populateForm() {
                this.processCategoryForm = {
                    id: this.model.id,
                    code: this.model.code,
                    name: this.model.name,
                }
            }
        },
        computed: {
            hasModel() {
                return this.model && this.model.id
            },
            dialogTitle() {
                return this.model && this.model.id ? 'Update Process Category' : 'Create New Process Category'
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
            }
        }
    }
</script>

<style scoped>

</style>
