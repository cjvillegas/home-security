<template>
    <div>
        <global-page-header title="My Profile"></global-page-header>

        <el-card
            class="box-card mt-3">
            <div class="row">
                <div class="col-md-6">
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
                    <el-form>
                        <el-form-item
                            label="New Password">

                        </el-form-item>
                        <el-form-item
                            label="Repeat New Password">

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
    export default {
        name: "PasswordIndex",
        data() {
            return {
                user: {},
                form: this.getDefaultFieldValues(),
                passwordForm: {
                    password: null,
                    password_confirmation: null
                },
                user: null
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
                axios.post('/profile/profile', this.form)
                .then((res) => {

                })
            },

            updatePassword() {
                axios.post('/profile/password', this.passwordForm)
                .then((res) => {

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
