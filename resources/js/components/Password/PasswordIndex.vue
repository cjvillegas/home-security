<template>
    <div>
        <global-page-header title="My Profile"></global-page-header>

        <el-card
            class="box-card mt-3"
            v-loading="loading">
            <div class="row">
                <div
                    class="col-md-6">
                    <el-form>
                        <el-form-item
                            label="Name">
                            <el-input
                                v-model="form.name"
                                placeholder="John Doe">

                            </el-input>
                        </el-form-item>
                        <el-form-item
                            label="Email">
                            <el-input
                                v-model="form.email">
                            </el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button
                                type="primary"
                                @click="updateProfile">
                                Save
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
                <div class="col-md-6">
                    <el-form
                        ref="passwordForm"
                        :model="passwordForm"
                        :rules="rules">
                        <el-form-item
                            prop="password"
                            :error="hasError('password')"
                            label="New Password">
                            <el-input
                                v-model="passwordForm.password">
                            </el-input>
                        </el-form-item>
                        <el-form-item
                            prop="password_confirmation"
                            :error="hasError('password_confirmation')"
                            label="Repeat New Password">
                            <el-input
                                v-model="passwordForm.password_confirmation">
                            </el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button
                                type="primary"
                                @click="updatePassword">
                                Save
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>
                <div class="col-md-6">
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
    import axios from 'axios'
    import {formHelper} from "../../mixins/formHelper"
    export default {
        name: "PasswordIndex",
        mixins: [formHelper],
        data() {
            return {
                user: {},
                form: this.getDefaultFieldValues(),
                passwordForm: {
                    password: null,
                    password_confirmation: null
                },
                rules: {
                    password: {required: true, message: 'Password is required.', trigger: ['blur', 'change']},
                    password_confirmation: {required: true, message: 'Repeat Password is Required', trigger: ['blur', 'change']},
                },
                user: null,
                loading: false
            }
        },

        created() {
            this.getAuthUser()
        },

        methods: {
            getAuthUser() {
                this.$API.User.getAuthUser()
                .then(res => {
                    this.user = res.data
                })
            },

            updateProfile() {
                this.loading = true
                axios.post('/profile/profile', this.form)
                .then((res) => {
                    this.$notify({
                        title: 'Success',
                        message: res.data.message,
                        type: 'success'
                    })
                    this.loading = true
                })
            },

            updatePassword() {
                this.$refs.passwordForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()
                        this.loading = true
                        axios.post('/profile/password', this.passwordForm)
                        .then((res) => {
                            this.$notify({
                                title: 'Success',
                                message: res.data.message,
                                type: 'success'
                            })
                            this.loading = true
                        })
                    }
                })

            },

            getDefaultFieldValues() {
                return {
                    name: null,
                    email: null
                }
            }
        },

        watch: {
            user: {
                handler() {
                    this.form.name = this.$root.user.name,
                    this.form.email = this.$root.user.email
                },
                immediate: true
            }
        }
    }
</script>
