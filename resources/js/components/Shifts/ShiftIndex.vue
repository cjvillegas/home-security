<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Shifts List</h4>
        </el-card>
        <div v-loading="loading">
            <el-card class="box-card mt-3">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Shifts..."
                            style="width: 250px"
                            @keyup.enter.native.prevent="fetchShifts">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="addNew">
                            <i class="fas fa-plus"></i> Add Shift
                        </el-button>
                    </div>
                </div>
                <el-table
                    :data="shifts"
                    class="w-100"
                    fit>

                    <el-table-column
                        prop="id"
                        label="ID"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="name"
                        label="Name"
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
                                    @confirm="deleteShift(scope.row.id)"
                                    confirm-button-text='OK'
                                    cancel-button-text='No, Thanks'
                                    icon="el-icon-info"
                                    icon-color="red"
                                    title="Are you sure to delete this Shift?">
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
            :title="dialogTitle"
            width="40%"
            @close="clearForm">
            <el-form
                v-loading="loading"
                ref="form"
                :model="form"
                :rules="rules">
                <el-form-item
                    label="Shift Name"
                    prop="name"
                    :error="hasError('name')">
                    <el-input
                        v-model="form.name"
                        placeholder="Shift One"
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
                loading: false,
                formDialogVisible: false,
                dialogType: 'Add',
                shifts: [],
                filters: {
                    searchString: null
                },
                form: this.getDefaultValues(),
                rules: {
                    name: {required: true, message: 'Name is required', trigger: ['blur', 'change']},
                },
            }
        },

        computed: {
            dialogTitle() {
                return (this.dialogType == 'Add') ? 'Add Shift' : (this.dialogType == 'Edit') ? 'Edit Shift' : 'View Shift'
            }
        },

        created() {
            this.filters.size = 10
            this.functionName = 'fetchShifts'
        },

        mounted() {
            this.fetchShifts()
        },

        methods: {
            fetchShifts() {
                this.loading = true

                this.$API.Shift.getList(this.filters)
                .then((response) => {
                    this.shifts = response.data.shifts.data
                    this.filters.total = response.data.shifts.total
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

            saveShift() {
                this.loading = true

                this.$API.Shift.save(this.form)
                .then((response) => {
                    switch(response.status){
                        case 200:
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchShifts()
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

            updateShift() {
                this.loading = true

                this.$API.Shift.update(this.form)
                .then((response) => {
                    switch(response.status){
                        case 200:
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchShifts()
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
                            this.updateShift()

                            return
                        }
                        this.saveShift()
                    }
                })
            },

            openEditDialog(item) {
                this.dialogType = 'Edit'
                this.form.id = item.id
                this.form.name = item.name
            },

            deleteShift(id) {
                let apiUrl = `/admin/shifts/${id}`
                this.$API.Shift.delete(id)
                .then( (response) => {
                    this.$notify({
                        title: 'Deleted!',
                        message: response.data.message,
                        type: 'success'
                    });
                    this.fetchShifts()
                })
            },

            clearForm() {
                if (this.$refs.form) {
                    this.$refs.form.clearValidate()
                }

                this.form = this.getDefaultValues()

                this.formDialogVisible = false
            },

            getDefaultValues() {
                return {
                    id: null,
                    name: null
                }
            }
        }
    }
</script>
