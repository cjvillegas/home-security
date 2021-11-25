<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Add Email"
        :before-close="closeForm"
        width="30%">
        <el-form
            ref="form"
            :model="form"
            :rules="rules"
            v-loading="loading">
            <el-form-item
                label="Name"
                prop="name"
                :error="hasError('name')">
                <el-input
                    v-model="form.name"
                    clearable
                    placeholder="John Doe"
                    class="w-100">
                </el-input>
            </el-form-item>
            <el-form-item
                label="Email"
                prop="email"
                :error="hasError('email')">
                <el-input
                    v-model="form.email"
                    clearable
                    placeholder="example@test.com"
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
    import {dialog} from "../../../../mixins/dialog";
    import {formHelper} from "../../../../mixins/formHelper";
    export default {
        name: "EmailForm",
        mixins: [dialog, formHelper],
        data() {
            return {
                form: this.getDefaultFieldValues(),
                rules: {
                    email: {required: true, message: 'Email is Required.', trigger: ['blur', 'change']},
                    name: {required: true, message: 'Name is Required.', trigger: ['blur', 'change']},
                },
            }
        },
        computed: {
            ...mapGetters('remakeChecker', ['loading']),
        },
        methods: {
            ...mapActions('remakeChecker', ['storeEmail', 'getEmails']),
            validate() {
                this.$refs.form.validate(valid => {
                    if (valid) {
                        this.resetErrors()
                        this.storeEmail(this.form)
                        .then(()=> {
                            this.$notify({
                                title: 'Success',
                                message: `Employee successfully saved.`,
                                type: 'success'
                            })
                            this.getEmails({})
                            this.closeForm()
                        })
                    }
                })
            },
            closeForm() {
                this.resetForm()
                this.closeModal()
            },
            resetForm() {
                this.form = this.getDefaultFieldValues()
            },
            getDefaultFieldValues() {
                return {
                    name: null,
                    email: null
                }
            }
        }
    }
</script>
