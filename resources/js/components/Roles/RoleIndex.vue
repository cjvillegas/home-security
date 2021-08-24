<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Roles List</h4>
        </el-card>
        <div v-loading="loading">
            <el-card class="box-card mt-3">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Roles..."
                            style="width: 250px"
                            @keyup.enter.native.prevent="fetchRoles">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="addNew">
                            <i class="fas fa-plus"></i> Add Role
                        </el-button>
                    </div>
                </div>
                <el-table
                    :data="roles"
                    class="w-100"
                    fit>

                    <el-table-column
                        prop="id"
                        label="ID"
                        width="150"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="title"
                        label="Title"
                        width="200"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        label="Permissions">
                    <template slot-scope="scope">
                        <el-tag
                            type="primary"
                            class="m-1"
                            v-for="permission in scope.row.permissions" :key="permission.id">
                            {{ permission.title }}
                        </el-tag>
                    </template>
                    </el-table-column>

                    <el-table-column
                        width="100%"
                        label="Action"
                        class-name="table-action-button">
                        <template slot-scope="scope">
                            <template>
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="Edit"
                                    placement="top"
                                    :open-delay="1000">
                                    <el-button
                                        @click="openEditDialog(scope.row), formDialogVisible = true"
                                        type="text">
                                        <i class="fas fa-pen"></i>
                                    </el-button>
                                </el-tooltip>
                                <el-popconfirm
                                    @confirm="deleteRole(scope.row.id)"
                                    confirm-button-text='OK'
                                    cancel-button-text='No, Thanks'
                                    icon="el-icon-info"
                                    icon-color="red"
                                    title="Are you sure to delete this Role?">
                                    <el-button
                                        type="text"
                                        class="text-danger ml-2"
                                        slot="reference">
                                        <i class="fas fa-trash-alt"></i>
                                    </el-button>
                                </el-popconfirm>
                            </template>
                        </template>
                    </el-table-column>

                </el-table>

                <div class="text-right">
                    <el-pagination
                        class="mt-3"
                        background
                        layout="total, sizes, prev, pager, next"
                        :total="filters.total"
                        :page-size="filters.size"
                        :page-sizes="[10, 25, 50, 100]"
                        :current-page="filters.page"
                        @size-change="handleSize"
                        @current-change="handlePage">
                    </el-pagination>
                </div>
            </el-card>
        </div>
        <el-dialog
            :visible.sync="formDialogVisible"
            :title="(dialogType == 'Add') ? 'Add Role' : (dialogType == 'Edit') ? 'Edit Role' : 'View Role'"
            width="50%"
            @close="clearForm">
            <el-form
                v-loading="loading"
                ref="form"
                :model="form"
                :rules="rules">
                <el-form-item
                    label="Team Name"
                    prop="title"
                    :error="hasError('title')">
                    <el-input
                        v-model="form.title"
                        placeholder="Team Alpha"
                        clearable
                        class="w-100">
                    </el-input>
                </el-form-item>

                <el-form-item
                    label="Permissions"
                    prop="permissions"
                    :error="hasError('permissions')">
                    <el-checkbox
                        class="float-right"
                        v-model="is_select_all"
                        @change="toggleSelectAll"
                        label="Select All">
                    </el-checkbox>
                    <el-select
                        v-model="form.permissions"
                        multiple
                        placeholder="Select permissions"
                        class="w-100">
                        <el-option
                            v-for="item in permissions"
                            :key="item.id"
                            :label="item.title"
                            :value="item.id">
                        </el-option>

                    </el-select>
                </el-form-item>
            </el-form>
            <span
                slot="footer"
                class="dialog-footer"
                v-if="this.dialogType != 'View'">
                <el-button
                    @click="clearForm">
                    Cancel
                </el-button>
                <el-button
                    type="primary"
                    @click="validate"
                    v-show="this.dialogType == 'Add'">
                    Save
                </el-button>
                <el-button
                    type="primary"
                    @click="validate"
                    v-show="this.dialogType == 'Edit'">
                    Update
                </el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import pagination from '../../mixins/pagination'
    import { formHelper } from '../../mixins/formHelper'

    export default {
        mixins: [pagination, formHelper],
        data() {
            return {
                loading: false,
                dialogType: false,
                formDialogVisible: false,
                roles: [],
                permissions: [],
                form: this.getDefaultValues(),
                filters: {
                    searchString: null,
                },

                rules: {
                    title: {required: true, message: 'Name is required', trigger: ['blur', 'change']},
                    permissions: {required: true, message: 'Permissions is required', trigger: ['blur', 'change']},
                },

                is_select_all: false
            }
        },

        created() {
            this.filters.size = 10
            this.functionName = 'fetchRoles'
        },

        mounted() {
            this.fetchRoles()
            this.fetchPermissions()
        },

        methods: {
            fetchRoles() {
                this.loading = true

                this.$API.Role.getList(this.filters)
                .then((response) => {
                    this.roles = response.data.roles.data
                    this.filters.total = response.data.roles.total
                })
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {
                    this.loading = false
                })
            },

            fetchPermissions() {
                this.loading = true

                this.$API.Role.getPermissions()
                .then((response) => {
                    this.permissions = response.data.permissions
                    console.log(this.permissions)
                })
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {
                    this.loading = false
                })
            },

            addNew() {
                this.clearForm()
                this.dialogType = 'Add'
                this.formDialogVisible = true
            },

            saveRole() {
                this.loading = true

                this.$API.Role.save(this.form)
                .then((response) => {
                    switch(response.status){
                        case 200:
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchRoles()
                            this.clearForm()
                    }
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        this.setErrors(err.response.data.errors)
                    }
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            updateRole() {
                this.loading = true

                this.$API.Role.update(this.form)
                .then((response) => {
                    switch(response.status){
                        case 200:
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchRoles()
                            this.clearForm()
                    }
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        this.setErrors(err.response.data.errors)
                    }
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            validate() {
                this.$refs.form.validate(valid => {
                    if (valid) {
                        this.resetErrors()
                        if (this.dialogType == 'Edit') {
                            this.updateRole()

                            return
                        }
                        this.saveRole()
                    }
                })
            },

            openEditDialog(item) {
                this.clearForm()
                this.dialogType = 'Edit'
                this.form.id = item.id
                this.form.title = item.title
                item.permissions.forEach(permission => {
                    this.form.permissions.push(permission.id)
                });
            },

            deleteRole(id) {
                this.$API.Role.delete(id)
                .then( (response) => {
                    this.$notify({
                        title: 'Deleted!',
                        message: response.data.message,
                        type: 'success'
                    });
                    this.fetchRoles()
                })
            },

            toggleSelectAll() {
                if(this.is_select_all) {
                    if(this.form.permissions.length <=0) {
                        this.permissions.forEach(permission => {
                            this.form.permissions.push(permission.id)
                        });
                    }
                }else {
                    this.form.permissions = []
                }
            },

            clearForm() {
                if (this.$refs.form) {
                    this.$refs.form.clearValidate()
                }

                this.is_select_all = false
                this.form = this.getDefaultValues()

                this.formDialogVisible = false
            },

            getDefaultValues() {
                return {
                    id: null,
                    title: null,
                    permissions: []
                }
            }
        }
    }
</script>
