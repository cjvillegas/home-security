<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Quality Codes</h4>
        </el-card>

        <div v-loading="loading">
            <el-card class="box-card mt-3">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search QC Codes..."
                            style="width: 250px"
                            @keyup.enter.native.prevent="fetchQualityControls">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-popover
                            placement="bottom-start"
                            width="350"
                            trigger="click"
                            class="mr-2">
                            <label>Status</label>
                            <el-select
                                v-model="filters.status"
                                class="w-100">
                                <el-option label="Active" :value="1"></el-option>
                                <el-option label="Deactivated" :value="0"></el-option>
                                <el-option label="Show All" :value="null"></el-option>
                            </el-select>

                            <el-button
                                @click="applyFilters"
                                type="primary"
                                class="w-100 mt-4">
                                Apply Filter
                            </el-button>

                            <el-button
                                slot="reference">
                                <i class="fas fa-filter"></i>
                            </el-button>
                        </el-popover>

                        <el-button
                            type="primary"
                            @click="addNew">
                            <i class="fas fa-plus"></i> Add Quality Control
                        </el-button>
                    </div>
                </div>
                <el-table
                    fit
                    :data="qualityControls">
                    <el-table-column
                        prop="id"
                        label="ID"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="qc_code"
                        label="QC Code"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="description"
                        label="Description">
                    </el-table-column>

                    <el-table-column
                        label="Status"
                        prop="is_active"
                        show-overflow-tooltip>
                        <template slot-scope="scope">
                            <el-tag
                                size="mini"
                                :type="scope.row.is_active ? 'success' : 'danger'"
                                effect="dark">
                                {{ scope.row.is_active ? 'Active' : 'Deactivated' }}
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
                                        @click="openEditDialog(scope.row)"
                                        type="text">
                                        <i class="fas fa-pen"></i>
                                    </el-button>
                                </el-tooltip>
                                <el-popconfirm
                                    @confirm="deleteQualityControl(scope.row.id)"
                                    confirm-button-text='OK'
                                    cancel-button-text='No, Thanks'
                                    icon="el-icon-info"
                                    icon-color="red"
                                    title="Are you sure to delete this QC code?">
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
                        :total="pagination.total"
                        :page-size="pagination.size"
                        :page-sizes="[10, 25, 50, 100]"
                        :current-page="pagination.page"
                        @size-change="handleSize"
                        @current-change="handlePage">
                    </el-pagination>
                </div>
            </el-card>
        </div>

         <el-dialog
            :visible.sync="formDialogVisible"
            :title="(dialogType == 'Add') ? 'Add Quality Control' : 'Edit Quality Control'"
            top="5vh"
            width="40%"
            @close="clearForm">
            <el-form
                ref="form"
                :model="form"
                :rules="rules">
                <el-form-item
                    label="QC Code"
                    prop="qc_code"
                    :error="hasError('qc_code')">
                    <el-input
                        placeholder="ABC"
                        v-model="form.qc_code">
                    </el-input>
                </el-form-item>

                <el-form-item
                    label="Description"
                    prop="description"
                    :error="hasError('description')">
                    <el-input
                        type="textarea"
                        :rows="2"
                        autosize
                        placeholder="Add a description"
                        v-model="form.description">
                    </el-input>
                </el-form-item>

                <el-form-item
                    label="Status"
                    prop="is_active">
                    <el-switch v-model="form.is_active"></el-switch>
                </el-form-item>
            </el-form>

            <span
                slot="footer"
                class="dialog-footer">
                    <el-button @click="formDialogVisible = false">
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
                qualityControls: [],
                loading: false,
                formDialogVisible: false,
                dialogType: 'Add',
                dialogTitle: 'Add Quality Control',
                filters: {
                    searchString: null,
                    status: 1
                },
                form: this.getDefaultFieldValues(),
                rules: {
                    qc_code: {required: true, message: 'Quality Code is required', trigger: 'change'},
                },
            }
        },

        mounted() {
            this.pagination.size = 10
            this.functionName = 'fetchQualityControls'
            this.fetchQualityControls()
        },

        methods: {
            applyFilters() {
                this.pagination.page = 1

                this.fetchQualityControls()
            },

            fetchQualityControls() {
                let apiUrl = `/admin/quality-control/list`
                this.loading = true
                let params = {...this.filters, ...this.pagination}
                axios.post(apiUrl, params)
                .then((response) => {
                    this.qualityControls = response.data.qualityControls.data
                    this.pagination.total = response.data.qualityControls.total
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            addNew() {
                this.dialogType = 'Add'
                this.clearForm()
                this.formDialogVisible = true
            },

            validate() {
                this.$refs.form.validate(valid => {
                    if (valid) {
                        this.resetErrors()
                        if (this.dialogType == 'Edit') {
                            this.updateQualityControl()

                            return
                        }

                        this.saveQualityControl()
                    }
                })
            },

            saveQualityControl() {
                let apiUrl = `/admin/quality-control/store`
                this.loading = true
                axios.post(apiUrl, this.form)
                .then((response) => {
                    switch(response.status) {
                        case 200:
                            this.formDialogVisible = false
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.clearForm()
                            this.fetchQualityControls()
                    }
                }).catch( err => {
                    if (err.response.status === 422) {
                        this.setErrors(err.response.data.errors)
                    }
                }).finally(_ => {
                    this.loading = false
                })
            },

            updateQualityControl() {
                let apiUrl = `/admin/quality-control/${this.form.id}/update`

                axios.patch(apiUrl, this.form)
                .then((response) => {
                    switch(response.status) {
                        case 200:
                            this.formDialogVisible = false
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.clearForm()
                            this.fetchQualityControls()
                    }
                }).catch( err => {
                    if (err.response.status === 422) {
                        this.setErrors(err.response.data.errors)
                    }
                }).finally(_ => {
                    this.loading = false
                })
            },

            openEditDialog(item) {
                this.dialogType = 'Edit'
                this.formDialogVisible = true
                this.setErrors([])
                this.form.id = item.id
                this.form.qc_code = item.qc_code
                this.form.description = item.description
                this.form.is_active = item.is_active
            },

            deleteQualityControl(id) {
                let apiUrl = `/admin/quality-control/${id}/destroy`
                axios.delete(apiUrl)
                .then( (response) => {
                    this.$notify({
                        title: 'QC code Deleted!',
                        message: response.data.message,
                        type: 'success'
                    });
                    this.fetchQualityControls()
                })
            },

            clearForm() {
                if (this.$refs.form) {
                    this.$refs.form.clearValidate()
                }
                this.form = this.getDefaultFieldValues()
            },

            getDefaultFieldValues() {
                return {
                    id: null,
                    qc_code: null,
                    description: null,
                    is_active: true,
                }
            }
        }
    }
</script>
