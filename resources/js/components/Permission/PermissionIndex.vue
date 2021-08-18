<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Permission List</h4>
        </el-card>
        <div v-loading="loading">
            <el-card class="box-card mt-3">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Permission..."
                            style="width: 250px"
                            @keyup.enter.native.prevent="fetchPermissions">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="addNew">
                            <i class="fas fa-plus"></i> Add Permission
                        </el-button>
                    </div>
                </div>

                <el-table
                    :data="permissions"
                    class="w-100"
                    fit>
                    <el-table-column
                        prop="title"
                        label="Title"
                        sortable>
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
                                    @confirm="deletePermission(scope.row.id)"
                                    confirm-button-text='OK'
                                    cancel-button-text='No, Thanks'
                                    icon="el-icon-info"
                                    icon-color="red"
                                    title="Are you sure to delete this Permission?">
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
            :title="(dialogType == 'Add') ? 'Add Permission' : (dialogType == 'Edit') ? 'Edit Permission' : 'View Permission'"
            width="40%"
            @close="clearForm">
            <el-form
                v-loading="loading"
                ref="form"
                :model="form"
                :rules="rules">
                <el-form-item
                    label="Permission Name"
                    prop="title"
                    :error="hasError('title')">
                    <el-input
                        v-model="form.title"
                        :disabled="this.dialogType == 'View'"
                        clearable
                        class="w-100">
                    </el-input>
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
                formDialogVisible: false,
                dialogType: 'Add',
                loading: false,
                filters: {
                    searchString: null,
                },
                form: {
                    id: null,
                    title: ''
                },
                rules: {
                    title: {required: true, message: 'Title is required', trigger: ['blur', 'change']},
                },
                permissions: []
            }
        },

        created() {
            this.filters.size = 10
            this.functionName = 'fetchPermissions'
        },

        mounted() {
            this.fetchPermissions()
        },

        methods: {
            fetchPermissions() {
                let apiUrl = `/admin/permissions/list`
                this.loading = true

                axios.post(apiUrl, this.filters)
                .then((response) => {
                    this.permissions = response.data.permissions.data
                    this.filters.total = response.data.permissions.total
                })
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {
                    this.loading = false
                })
            },

            savePermission() {
                let apiUrl = `/admin/permissions`
                this.loading = true

                axios.post(apiUrl, this.form)
                .then((response) => {
                    switch(response.status){
                        case 200:
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchPermissions()
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

            updatePermission() {
                let apiUrl = `/admin/permissions/${this.form.id}`
                this.loading = true

                axios.patch(apiUrl, this.form)
                .then((response) => {
                    switch(response.status){
                        case 200:
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchPermissions()
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

            addNew() {
                if(this.dialogType == 'Edit') {
                    this.clearForm()
                }
                this.dialogType = 'Add'
                this.formDialogVisible = true
            },

            openEditDialog(item) {
                this.dialogType = 'Edit'
                this.form.id = item.id
                this.form.title = item.title
            },

            deletePermission(id) {
                let apiUrl = `/admin/permissions/${id}`
                axios.delete(apiUrl)
                .then( (response) => {
                    this.$notify({
                        title: 'Deleted!',
                        message: response.data.message,
                        type: 'success'
                    });
                    this.fetchPermissions()
                })
            },

            validate() {
                this.$refs.form.validate(valid => {
                    if (valid) {
                        this.resetErrors()
                        if (this.dialogType == 'Edit') {
                            this.updatePermission()

                            return
                        }
                        this.savePermission()
                    }
                })
            },

            clearForm() {
                if (this.$refs.form) {
                    this.$refs.form.clearValidate()
                }

                this.form.title = null

                this.formDialogVisible = false
            }
        }
    }
</script>
