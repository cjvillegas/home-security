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
                label="Serial ID"
                prop="serial_id"
                :error="hasError('serial_id')"
                class="mb-0">
                <el-input
                    v-model="importForm.serial_id"
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
                    serial_id: null
                },
                rules: {
                    serial_id: {required: true, message: 'Please provide the serial ID that you want to import.', trigger: 'blur'}
                }
            }
        },

        computed: {
            disableImport() {
                return !this.importForm.serial_id
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

                this.$API.Orders.importFromBlind(this.importForm.serial_id)
                .then(res => {
                    if (res.data && res.data.id) {
                        this.$notify.success({
                            title: 'Order Import',
                            message: "Order successfully imported from BlindData."
                        });

                        this.$emit('imported', res.data)
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

            closeForm() {
                this.importForm.serial_id = null

                if (this.$refs.importForm) {
                    this.$refs.importForm.clearValidate()
                }

                this.closeModal()
            }
        }
    }
</script>
