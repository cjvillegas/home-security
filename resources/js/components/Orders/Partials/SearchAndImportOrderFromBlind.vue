<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Import Order From Blind Wizard"
        @close="closeForm"
        width="400px"
        custom-class="sbg-compact-modal">
        <el-form
            :model="importForm"
            :rules="rules"
            v-loading="loading"
            ref="importForm">
            <el-form-item
                label="Search By"
                prop="field"
                :error="hasError('field')"
                class="mb-3">
                <el-select
                    v-model="importForm.field"
                    placeholder="Select to search field"
                    class="w-100">
                    <el-option value="order_no" label="Order No."></el-option>
                    <el-option value="serial_id" label="Serial ID"></el-option>
                </el-select>
            </el-form-item>

            <el-form-item
                label="Serial ID"
                prop="value"
                :error="hasError('value')"
                class="mb-0">
                <el-input
                    v-model="importForm.value"
                    clearable
                    placeholder="Enter serial id"
                    class="w-100">
                </el-input>
            </el-form-item>

            <el-input
                hidden
                class="w-100">
            </el-input>
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
                :disabled="disableImport || loading"
                type="primary"
                class="btn-primary">
		    	Import
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
    import { dialog } from "../../../mixins/dialog"
    import { formHelper } from "../../../mixins/formHelper"

    export default {
        name: "SearchAndImportOrderFromBlind",

        mixins: [dialog, formHelper],

        data() {
            return {
                loading: false,
                importForm: {
                    field: 'order_no',
                    value: null
                },
                rules: {
                    field: {required: true, message: 'Please provide field that you want to import.', trigger: 'change'},
                    value: {required: true, message: 'Please provide the value that you want to import.', trigger: 'blur'}
                }
            }
        },

        computed: {
            disableImport() {
                return !this.importForm.value || !this.importForm.field
            }
        },

        methods: {
            validate() {
                this.resetErrors()

                if (this.disableImport) {
                    this.$notify.error({
                        title: 'Order Import',
                        message: "Please provide the serial ID that you want to import."
                    });

                    return
                }

                this.importOrder()
            },

            importOrder() {
                this.loading = true

                this.$API.Orders.importFromBlind(this.importForm.field, this.importForm.value)
                .then(res => {
                    this.$notify.success({
                        title: 'Order Import',
                        message: "Order successfully imported from BlindData."
                    });

                    this.$emit('imported')

                    setTimeout(_ => {
                        this.closeForm()
                    }, 300)
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

            closeForm() {
                this.importForm.field = 'order_no'
                this.importForm.value = null

                if (this.$refs.importForm) {
                    this.$refs.importForm.clearValidate()
                }

                this.closeModal()
            }
        }
    }
</script>
