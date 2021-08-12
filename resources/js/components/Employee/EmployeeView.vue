<template>
    <div v-loading="loading">
        <el-card class="box-card">
            <div class="d-flex align-items-center">
                <el-button
                    @click="backToList">
                    <i class="fas fa-arrow-left"></i> Back to List
                </el-button>

                <h4 class="mb-0 ml-3">{{ employeeName | ucWords }}</h4>
            </div>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div v-if="employee">
                <el-descriptions
                    class="margin-top"
                    :column="2"
                    size="medium"
                    direction="vertical"
                    border>
                    <template slot="title">
                        {{ `${employeeName} Information` | ucWords }}
                        <el-tag
                            size="mini"
                            :type="employee.is_active ? 'success' : 'danger'"
                            effect="dark">
                            {{ employee.is_active ? 'Active' : 'Deactivated' }}
                        </el-tag>
                    </template>
                    <template slot="extra">
                        <el-button
                            @click="changeStatus"
                            :type="employee.is_active ? 'danger' : 'success'"
                            size="small">
                            {{ employee.is_active ? 'Deactivated' : 'Activate' }}
                        </el-button>

                        <el-button
                            @click="deleteEmployee"
                            type="danger"
                            size="small">
                            Delete
                        </el-button>
                    </template>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-address-card"></i>
                            ID
                        </template>
                        {{ employee.id | numFormat }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-barcode"></i>
                            Barcode
                        </template>
                        {{ employee.barcode }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="el-icon-user"></i>
                            Full Name
                        </template>
                        {{ employeeName | ucWords }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-hand-pointer"></i>
                            Pin Code
                        </template>
                        {{ employee.pin_code }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-users"></i>
                            Team
                        </template>
                        {{ employee.team ? employee.team.name : '' | ucWords }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-clock"></i>
                            Shift
                        </template>
                        {{ employee.shift ? employee.shift.name : '' | ucWords }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-user-clock"></i>
                            Working Hours
                        </template>
                        {{ employee.standard_working_hours }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-user-lock"></i>
                            Clock No.
                        </template>
                        {{ employee.clock_num }}
                    </el-descriptions-item>
                </el-descriptions>
            </div>

            <el-empty
                v-else
                description="No Employee Found. The employee might be deleted or not existing in your company.">
                <el-button
                    @click="backToList">
                    Back to List
                </el-button>
            </el-empty>
        </el-card>
    </div>
</template>

<script>
    export default {
        name: "EmployeeView",
        props: {
            id: {
                required: true
            }
        },
        data() {
            return {
                employee: null,
                loading: false
            }
        },
        computed: {
            employeeName() {
                return this.employee ? this.employee.fullname : ''
            }
        },
        created() {
            if (this.id) {
                this.getEmployee()
            }
        },
        methods: {
            getEmployee() {
                this.loading = true

                this.$API.Employee.show(this.id)
                    .then(res => {
                        this.employee = res.data
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            changeStatus() {
                let status = this.employee.is_active ? 'Deactivate' : 'Activate'

                this.$confirm(`Are you sure you want to ${status} this employee?`, 'Confirm', {
                    confirmButtonText: "Yes, I'm Sure",
                    cancelButtonText: 'No, Not Sure',
                    type: 'warning',
                    confirmButtonClass: 'el-button--warning'
                })
                    .then(_ => {
                        this.loading = true

                        this.$API.Employee.changeStatus(this.employee.id)
                            .then(res => {
                                if (res.data) {
                                    this.employee.is_active = res.data.is_active

                                    this.$notify({
                                        title: 'Success',
                                        message: `Employee successfully ${status}.`,
                                        type: 'success'
                                    })
                                }
                            })
                            .catch(err => {
                                console.log(err)
                            })
                            .finally(_ => {
                                this.loading = false
                            })
                    })
                    .catch(_ => {})

            },
            deleteEmployee() {
                this.$confirm('Are you sure you want to delete this employee?', 'Confirm', {
                    confirmButtonText: "Yes, I'm Sure",
                    cancelButtonText: 'No, Not Sure',
                    type: 'error',
                    confirmButtonClass: 'el-button--danger'
                })
                .then(_ => {
                    this.loading = true

                    this.$API.Employee.delete(this.employee.id)
                        .then(res => {
                            if (res.data) {
                                this.$notify({
                                    title: 'Success',
                                    message: 'Employee successfully deleted.',
                                    type: 'success'
                                })

                                setTimeout(_ => {
                                    this.backToList()

                                }, 300)
                            }
                        })
                        .catch(err => {
                            console.log(err)
                            this.loading = false
                        })
                })
                .catch(_ => {})

            },
            backToList() {
                this.$router.push({name: 'Employee List'})
            }
        },
        beforeRouteEnter(to, from, next) {
            if (to.params && !to.params.id) {
                next({replace: true, name: 'Employee List'})
            }

            next()
        },
    }
</script>
