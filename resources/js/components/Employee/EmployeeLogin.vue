<template>
    <div class="d-flex justify-content-center align-items-center h-100" v-loading="loading">
        <div class="login-container">
            <el-card>
                <h3 class="font-weight-bolder">Welcome {{ employee ? $StringService.ucwords(employee.fullname) : '' }}</h3>

                <el-alert
                    :title="title"
                    type="info"
                    :closable="false"
                    class="mt-3">
                </el-alert>

                <el-form
                    ref="loginForm"
                    :model="loginForm"
                    class="mt-3">
                    <el-form-item
                        v-if="step === 1"
                        label="Barcode"
                        prop="barcode"
                        :rules="{required: true, message: 'Please enter barcode', trigger: 'blur'}">
                        <el-input
                            @keyup.enter.prevent.native="validateForm"
                            v-model="loginForm.barcode"
                            clearable
                            placeholder="E10001"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-form-item
                        v-else
                        label="Pin Code"
                        prop="pin_code"
                        :rules="{required: true, message: 'Please enter pin code', trigger: 'blur'}">
                        <el-input
                            @keyup.enter.prevent.native="validateForm"
                            show-password
                            v-model="loginForm.pin_code"
                            clearable
                            placeholder="1997"
                            class="w-100">
                        </el-input>
                    </el-form-item>

                    <el-input
                        hidden
                        class="w-100">
                    </el-input>
                </el-form>

                <el-button
                    @click="validateForm"
                    type="primary"
                    class="w-100 mt-3">
                    {{ step === 1 ? 'Verify Barcode' : 'Login' }}
                </el-button>

                <el-button
                    v-if="step === 2"
                    @click="backToBarcodeSearch"
                    class="w-100 mt-2 ml-0">
                    Back
                </el-button>
            </el-card>
        </div>
    </div>
</template>

<script>
export default {
    name: "EmployeeLogin",
    data() {
        return {
            loading: false,
            step: 1,
            loginForm: {
                barcode: null,
                pin_code: null
            },
            employee: null
        }
    },
    methods: {
        validateForm() {
            this.$refs.loginForm.validate(valid => {
                if (valid) {
                    if (this.step === 1) {
                        this.getEmployeeByBarcode()
                        return
                    }

                    this.login()
                }
            })
        },

        getEmployeeByBarcode() {
            this.loading = true

            this.$API.EmployeeEmployee.getEmployeeByBarcode(this.loginForm.barcode)
                .then(res => {
                    this.employee = res.data
                    this.step++
                })
                .catch(err => {
                    this.$notify.error({
                        title: 'Not Found',
                        message: 'No employee found with the provided barcode'
                    });
                })
                .finally(_ => {
                    this.loading = false
                })
        },
        login() {
            this.loading = true

            this.$API.EmployeeAuth.login(this.loginForm)
            .then(res => {
                if (res.data) {
                    window.location.href = '/employee/index'
                }
            })
            .catch(err => {
                console.log(err)

                if (err.response.status === 404) {
                    this.$notify.error({
                        title: 'Not Found',
                        message: 'No user or employee found.'
                    });
                }
            })
            .finally(_ => {
                this.loading = false
            })
        },
        backToBarcodeSearch() {
            this.step--
            this.employee = null
        }
    },
    computed: {
        title() {
            return this.step === 1 ? 'Please scan or enter your employee barcode to begin.' : 'Please scan or enter your pin code to login.'
        }
    }
}
</script>

<style scoped>
    .login-container {
        width: 30rem;
    }
</style>
