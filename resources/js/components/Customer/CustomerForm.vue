<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        :before-close="closeForm"
        width="60%">
        <el-form
            ref="customerForm"
            :model="customerForm"
            :rules="rules"
            v-loading="loading">
            <el-form-item
                label="Account Code"
                prop="account_code"
                :error="hasError('account_code')">
                <el-input
                    v-model="customerForm.account_code"
                    :disabled="isView"
                    clearable
                    placeholder="ABC1234"
                    class="w-100">
                </el-input>
            </el-form-item>
            <el-form-item
                label="Company Name"
                prop="company_name"
                :error="hasError('company_name')">
                <el-input
                    v-model="customerForm.company_name"
                    :disabled="isView"
                    clearable
                    placeholder="ABC1234 SOLUTIONS LTD"
                    class="w-100">
                </el-input>
            </el-form-item>
            <el-form-item
                label="Zoho CRM ID"
                prop="zoho_crm_id"
                :error="hasError('zoho_crm_id')">
                <el-input
                    v-model="customerForm.zoho_crm_id"
                    :disabled="isView"
                    clearable
                    placeholder="0123456789"
                    class="w-100">
                </el-input>
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
                v-if="!isView"
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
    import { mapActions, mapGetters } from 'vuex';
    import {dialog} from "../../mixins/dialog";
    import {formHelper} from "../../mixins/formHelper";
    export default {
        name: "CustomerForm",
        mixins: [dialog, formHelper],
        props: {
            model: {},
            isView: Boolean
        },
        data() {
            return {
                customerForm: this.getDefaultFieldValues(),
                rules: {
                    account_code: {required: true, message: 'Account Code field is required.', trigger: ['blur', 'change']},
                    company_name: {required: true, message: 'Company Name field is required.', trigger: ['blur', 'change']},
                    zoho_crm_id: {required: true, message: 'Zoho CRM ID field is required.', trigger: ['blur', 'change']},
                }
            }
        },
        computed: {
            ...mapGetters('customers', ['loading']),
            hasModel() {
                return this.model && this.model.id
            },
            dialogTitle() {
                return (this.isView == true) ? 'View Customer' : this.hasModel ? 'Update Customer' : 'Create New Customer'
            },
            hasFormChange() {
                for (let x in this.customerForm) {
                    // if mode is create and the field is is_active, skip
                    if (!this.hasModel) {
                        continue
                    }

                    let form = this.customerForm[x]
                    let value = this.hasModel ? this.model[x] : null

                    if ((this.hasModel && form !== value) || (!this.hasModel && !!form)) {
                        return true
                    }
                }

                return false
            }
        },
        methods: {
            ...mapActions('customers', ['createNewCustomer', 'updateCustomer']),
            validate() {
                this.$refs.customerForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()

                        if (this.hasModel) {
                            this.updateCustomer(this.customerForm)

                            return
                        }

                        this.createNewCustomer(this.customerForm)
                        .then((response) => {
                            if (response.data) {
                                this.$notify({
                                    title: 'Success',
                                    message: response.data.message,
                                    type: 'success'
                                })
                                this.$EventBus.fire('CUSTOMER_CREATE')
                                setTimeout(_ => {
                                    this.closeForm(true)
                                }, 300)
                            }
                        })
                    }
                })
            },
            getDefaultFieldValues() {
                return {
                    id: null,
                    account_code: null,
                    company_name: null,
                    zoho_crm_id:null
                }
            },
            initializeForm() {
                if (!this.model || !this.model.id) {
                    return
                }

                this.customerForm = {
                    id: this.model.id,
                    account_code: this.model.account_code,
                    company_name: this.model.company_name,
                    zoho_crm_id: this.model.zoho_crm_id
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
                this.customerForm = this.getDefaultFieldValues()

                if (this.$refs.customerForm) {
                    setTimeout(_ => {
                        this.$refs.customerForm.clearValidate()
                    },200)
                }
            },
        },
        watch: {
            model(value) {
                console.log(value)
                if (value) {
                    this.initializeForm()
                }
            }
        }
    }
</script>
