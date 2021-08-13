<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        :before-close="closeForm"
        width="60%">
        <el-form
            ref="userForm"
            :model="userForm"
            :rules="rules"
            v-loading="loading">
            <div class="row">
                <div class="col-md-6">
                    <el-form-item
                        label="Name"
                        prop="name"
                        :error="hasError('name')">
                        <el-input
                            v-model="userForm.name"
                            clearable
                            placeholder="John Doe">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Email"
                        prop="email"
                        :error="hasError('email')">
                        <el-input
                            v-model="userForm.email"
                            clearable
                            placeholder="johndoe@email.com">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        label="Roles"
                        prop="roles"
                        :error="hasError('roles')">
                        <el-select
                            v-model="userForm.roles"
                            filterable
                            multiple
                            clearable
                            collapse-tags
                            class="w-100">
                            <el-option
                                v-for="role in roles"
                                :key="role.id"
                                :label="role.title | ucWords"
                                :value="role.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </div>
                <div class="col-md-6">
                    <el-form-item
                        v-if="hasModel"
                        label="Status"
                        prop="is_active">
                        <el-switch v-model="userForm.is_active"></el-switch>
                    </el-form-item>

                    <el-form-item
                        v-if="hasModel"
                        label="Update Password">
                        <el-switch v-model="updatePassword"></el-switch>
                    </el-form-item>

                    <template v-if="!hasModel || updatePassword">
                        <el-form-item
                            label="Password"
                            prop="password"
                            :rules="[
                                {required: true, message: 'Password field is required.', trigger: 'change'},
                                {min: 5, message: 'Please input at least 5 characters.', trigger: 'change'}
                            ]"
                            :error="hasError('password')">
                            <el-input
                                v-model="userForm.password"
                                show-password
                                clearable
                                placeholder="Please input password">
                            </el-input>
                        </el-form-item>

                        <el-form-item
                            label="Confirm Password"
                            prop="password_confirmation"
                            :error="hasError('password_confirmation')"
                            :rules="{required: true, validator: this.validateConfirmPassword, trigger: 'change'}">
                            <el-input
                                v-model="userForm.password_confirmation"
                                show-password
                                clearable
                                placeholder="Please input confirm password">
                            </el-input>
                        </el-form-item>
                    </template>
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
    import cloneDeep from 'lodash/cloneDeep'
    import {dialog} from "../../mixins/dialog";
    import {formHelper} from "../../mixins/formHelper";

    export default {
        name: "UserForm",
        mixins: [dialog, formHelper],
        props: {
            model: {},
            roles: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                updatePassword: false,
                loading: false,
                userForm: this.getDefaultFieldValues(),
                rules: {
                    name: {required: true, message: 'Name field is required.', trigger: ['blur', 'change']},
                    email: [
                        {type: 'email', message: 'Invalid email address.', trigger: ['blur', 'change']},
                        {required: true, message: 'Email field is required.', trigger: ['blur', 'change']},
                    ],
                    roles: {required: true, message: 'Please select at least one role.', trigger: 'change'}
                }
            }
        },
        computed: {
            hasModel() {
                return this.model && this.model.id
            },
            dialogTitle() {
                return this.hasModel ? 'Update User' : 'Create New User'
            },
            hasFormChange() {
                for (let x in this.userForm) {
                    // if mode is create and the field is is_active, skip
                    if (!this.hasModel && x === 'is_active') {
                        continue
                    }

                    let form = this.userForm[x]
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
                this.$refs.userForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()

                        if (this.hasModel) {
                            this.updateUser()

                            return
                        }

                        this.createNewUser()
                    }
                })
            },
            createNewUser() {
                this.loading = true

                this.$API.User.store(this.userForm)
                    .then(res => {
                        if (res.data) {
                            this.$EventBus.fire('USER_CREATE')

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
            updateUser() {
                this.loading = true

                this.$API.User.update(this.userForm, this.userForm.id)
                    .then(res => {
                        if (res.data) {
                            this.$EventBus.fire('USER_UPDATE')

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

                this.userForm = {
                    id: this.model.id,
                    name: this.model.name,
                    email: this.model.email,
                    roles: this.model.roles.reduce((acc, cur) => acc = [...acc, ...[cur.id]] , []),
                    password: null,
                    password_confirmation: null,
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
                this.userForm = this.getDefaultFieldValues()

                if (this.$refs.userForm) {
                    setTimeout(_ => {
                        this.$refs.userForm.clearValidate()
                    },200)
                }
            },
            getDefaultFieldValues() {
                return {
                    id: null,
                    name: null,
                    email: null,
                    roles: [],
                    password: null,
                    password_confirmation: null,
                    is_active: true,
                }
            },
            validateConfirmPassword(rule, value, callback) {
                // if input value is empty
                if (!value) {
                    callback(new Error('Confirm password field is required.'))
                }

                if (value.length < 5) {
                    callback(new Error('Please input at least 5 characters.'))
                }

                // if password and confirm password field does not match
                if (value !== this.userForm.password) {
                    callback(new Error('Password and confirm password field does not match.'))
                }

                callback()
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
