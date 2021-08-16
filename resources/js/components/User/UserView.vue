<template>
    <div v-loading="loading">
        <el-card class="box-card">
            <div class="d-flex align-items-center">
                <el-button
                    @click="backToList">
                    <i class="fas fa-arrow-left"></i> Back to List
                </el-button>

                <h4 class="mb-0 ml-3">{{ userName | ucWords }}</h4>
            </div>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div v-if="user">
                <el-descriptions
                    class="margin-top"
                    :column="1"
                    size="medium"
                    direction="vertical"
                    border>
                    <template slot="title">
                        {{ `${userName} Information` | ucWords }}
                        <el-tag
                            size="mini"
                            :type="user.is_active ? 'success' : 'danger'"
                            effect="dark">
                            {{ user.is_active ? 'Active' : 'Deactivated' }}
                        </el-tag>
                    </template>
                    <template slot="extra">
                        <el-button
                            @click="changeStatus"
                            :type="user.is_active ? 'danger' : 'success'"
                            size="small">
                            {{ user.is_active ? 'Deactivated' : 'Activate' }}
                        </el-button>

                        <el-button
                            @click="deleteUser"
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
                        {{ user.id | numFormat }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="el-icon-user"></i>
                            Name
                        </template>
                        {{ userName | ucWords }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-envelope"></i>
                            Email
                        </template>
                        {{ user.email }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-user-check"></i>
                            Email Verified At
                        </template>
                        {{ user.email_verified_at | fixDateByFormat }}
                    </el-descriptions-item>
                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-users"></i>
                            Roles
                        </template>
                        <el-tag
                            v-for="role in user.roles"
                            :key="role.id"
                            type="primary"
                            class="ml-2">
                            {{ role.title | ucWords }}
                        </el-tag>
                    </el-descriptions-item>
                </el-descriptions>
            </div>

            <el-empty
                v-else
                description="No User Found. The user might be deleted or not existing in your company.">
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
        name: "UserView",
        props: {
            id: {
                required: true
            }
        },
        data() {
            return {
                user: null,
                loading: false
            }
        },
        computed: {
            userName() {
                return this.user ? this.user.name : ''
            }
        },
        created() {
            if (this.id) {
                this.getUser()
            }
        },
        methods: {
            getUser() {
                this.loading = true

                this.$API.User.show(this.id)
                    .then(res => {
                        this.user = res.data
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            changeStatus() {
                let status = this.user.is_active ? 'Deactivate' : 'Activate'

                this.$confirm(`Are you sure you want to ${status} this user?`, 'Confirm', {
                    confirmButtonText: "Yes, I'm Sure",
                    cancelButtonText: 'No, Not Sure',
                    type: 'warning',
                    confirmButtonClass: 'el-button--warning'
                })
                    .then(_ => {
                        this.loading = true

                        this.$API.User.changeStatus(this.user.id)
                            .then(res => {
                                if (res.data) {
                                    this.user.is_active = res.data.is_active

                                    this.$notify({
                                        title: 'Success',
                                        message: `User successfully ${status}.`,
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
            deleteUser() {
                this.$confirm('Are you sure you want to delete this user?', 'Confirm', {
                    confirmButtonText: "Yes, I'm Sure",
                    cancelButtonText: 'No, Not Sure',
                    type: 'error',
                    confirmButtonClass: 'el-button--danger'
                })
                    .then(_ => {
                        this.loading = true

                        this.$API.User.delete(this.user.id)
                            .then(res => {
                                if (res.data) {
                                    this.$notify({
                                        title: 'Success',
                                        message: 'User successfully deleted.',
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
                this.$router.push({name: 'User List'})
            }
        },
        beforeRouteEnter(to, from, next) {
            if (to.params && !to.params.id) {
                next({replace: true, name: 'User List'})
            }

            next()
        },
    }
</script>
