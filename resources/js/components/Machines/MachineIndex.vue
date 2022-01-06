<template>
    <div>
        <el-card class="card">
            <div v-loading="loading">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Machines..."
                            @keyup.enter.native.prevent="fetchMachines"
                            style="width: 250px">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="addNew">
                            <i class="fas fa-plus"></i> Add Machine
                        </el-button>
                    </div>
                </div>
                <el-table
                    :data="machines"
                    class="w-100"
                    fit>
                        <el-table-column
                            prop="name"
                            label="Name"
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="serial_no"
                            label="Serial No."
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="location"
                            label="Location"
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="machine_target"
                            label="Machine Target"
                            sortable>
                            <template slot-scope="scope">
                                {{ scope.row.machine_target | numFormat }}
                            </template>
                        </el-table-column>
                        <el-table-column
                            prop="status"
                            label="Status"
                            sortable>
                            <template slot-scope="scope">
                                {{ scope.row.status ? 'Active' : 'Inactive' }}
                            </template>
                        </el-table-column>
                        <el-table-column
                            width="100%"
                            label="Action"
                            class-name="table-action-button">
                            <template slot-scope="scope">
                                <template>
                                    <el-button
                                    @click="viewMachine(scope.row)"
                                        type="text"
                                        class="ml-2 text-secondary">
                                        <i class="fas fa-eye"></i>
                                    </el-button>
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
                                        @confirm="deleteMachine(scope.row.id)"
                                        confirm-button-text='OK'
                                        cancel-button-text='No, Thanks'
                                        icon="el-icon-info"
                                        icon-color="red"
                                        title="Are you sure to delete this?">
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
            </div>
            <el-pagination
                class="custom-pagination-class  mt-3 float-right"
                background
                layout="total, sizes, prev, pager, next"
                :total="pagination.total"
                :page-size="pagination.size"
                :page-sizes="[10, 25, 50, 100]"
                :current-page="pagination.page"
                @size-change="handleSize"
                @current-change="handlePage">
            </el-pagination>
        </el-card>

        <el-dialog
            :visible.sync="formDialogVisible"
            :title="(edit === true) ? 'Edit Machine' : (edit === false) ? 'Add Machine' : 'View Machine'"
            width="70%"
            @close="clearForm">
            <el-form
                v-loading="loading"
                ref="form"
                :model="form"
                :rules="rules">
                <el-row
                    :gutter="20">
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Machine Name"
                            prop="name"
                            :error="hasError('name')">
                            <el-input
                                v-model="form.name"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Serial No."
                            prop="serial_no"
                            :error="hasError('serial_no')">
                            <el-input
                                v-model="form.serial_no"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Location"
                            prop="location"
                            :error="hasError('location')">
                            <el-input
                                placeholder="Location"
                                :disabled="this.edit === 'View'"
                                v-model="form.location"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Machine Target"
                            prop="machine_target"
                            :error="hasError('machine_target')">
                            <el-input
                                placeholder="Machine Target"
                                :disabled="this.edit === 'View'"
                                v-model="form.machine_target"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Status"
                            prop="status"
                            :error="hasError('status')">
                            <el-select
                                v-model="form.status"
                                placeholder="Status"
                                :disabled="this.edit === 'View'"
                                class="w-100">
                                <el-option
                                    label="Active"
                                    :value="true">
                                </el-option>

                                <el-option
                                    label="Inactive"
                                    :value="false">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Supplier"
                            prop="supplier"
                            :error="hasError('supplier')">
                            <el-input
                                v-model="form.supplier"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Model"
                            prop="model"
                            :error="hasError('model')">
                            <el-input
                                v-model="form.model"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 1"
                            prop="parameter_1"
                            :error="hasError('parameter_1')">
                            <el-input
                                v-model="form.parameter_1"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 2"
                            prop="parameter_2"
                            :error="hasError('parameter_2')">
                            <el-input
                                v-model="form.parameter_2"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 3"
                            prop="parameter_3"
                            :error="hasError('parameter_3')">
                            <el-input
                                v-model="form.parameter_3"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 4"
                            prop="parameter_4"
                            :error="hasError('parameter_4')">
                            <el-input
                                v-model="form.parameter_4"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 5"
                            prop="parameter_5"
                            :error="hasError('parameter_5')">
                            <el-input
                                v-model="form.parameter_5"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 6"
                            prop="parameter_6"
                            :error="hasError('parameter_6')">
                            <el-input
                                v-model="form.parameter_6"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 7"
                            prop="parameter_7"
                            :error="hasError('parameter_7')">
                            <el-input
                                v-model="form.parameter_7"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 8"
                            prop="parameter_8"
                            :error="hasError('parameter_8')">
                            <el-input
                                v-model="form.parameter_8"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 9"
                            prop="parameter_9"
                            :error="hasError('parameter_9')">
                            <el-input
                                v-model="form.parameter_9"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 10"
                            prop="parameter_10"
                            :error="hasError('parameter_10')">
                            <el-input
                                v-model="form.parameter_10"
                                :disabled="this.edit === 'View'"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <span
                slot="footer"
                class="dialog-footer"
                v-if="this.edit !== 'View'">
                <el-button
                    @click="clearForm">
                    Cancel
                </el-button>
                <el-button
                    type="primary"
                    @click="validate"
                    v-show="!edit">
                    Save
                </el-button>
                <el-button
                    type="primary"
                    @click="validate"
                    v-show="edit">
                    Update
                </el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep";
    import pagination from '../../mixins/pagination'
    import { formHelper } from '../../mixins/formHelper'

    export default {
        mixins: [pagination, formHelper],
        data() {
            return {
                edit: false,
                machines: [],
                form: this.getDefaultFieldValues,
                rules: {
                    name: {required: true, message: 'Name is required', trigger: ['blur', 'change']},
                    serial_no: {required: true, message: 'Serial No. is required', trigger: ['blur', 'change']},
                    location: {required: true, message: 'Location is required', trigger: ['blur', 'change']},
                    status: {required: true, message: 'Status is required', trigger: 'change'}
                },
                formDialogVisible: false,
                filters: {
                    searchString: ''
                },
                statusOption: [
                   {  id: 1, value: 'Active' },
                   {  id: 0, value: 'Inactive' }
                ],
                loading: false
            }
        },

        created() {
            this.pagination.size = 10
            this.functionName = 'fetchMachines'
        },

        mounted() {
            this.fetchMachines()
        },

        methods: {
            addNew() {
                this.clearForm()
                this.formDialogVisible = true
                this.edit = false
            },

            fetchMachines() {
                this.loading = true

                let params = {...this.filters, ...this.pagination}

                this.$API.Machine.fetch(params)
                .then ( (response) => {
                    this.machines = response.data.machines.data
                    this.pagination.total = response.data.machines.total
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            validate() {
                this.$refs.form.validate(valid => {
                    if (valid) {
                        this.resetErrors()
                        if (this.edit) {
                            this.updateMachine()

                            return
                        }

                        this.saveMachine()
                    }
                })
            },

            saveMachine() {
                this.loading = true

                this.$API.Machine.save(this.form)
                    .then((response) => {
                         switch(response.status){
                            case 200:
                                this.$notify({
                                    title: 'Success',
                                    message: response.data.message,
                                    type: 'success'
                                })
                                this.fetchMachines()
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

            updateMachine() {
                this.$confirm('You are about to edit this Machine. Continue?', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    type: 'info'
                }).then( () => {
                    this.loading = true

                    let apiUrl = `/admin/machines/${this.form.id}/update`
                    axios.patch(apiUrl, this.form)
                        .then((response) => {
                            this.$notify({
                                title: 'Success!',
                                message: response.data.message,
                                type: 'success'
                            });

                            this.fetchMachines()
                        })
                        .catch(err => {
                            if (err.response.status === 422) {
                                this.setErrors(err.response.data.errors)
                            }
                        })
                        .finally(_ => {
                            this.loading = false
                        })
                })
            },

            deleteMachine(id) {
                let apiUrl = `/admin/machines/${id}/destroy`
                axios.delete(apiUrl)
                    .then( (response) => {
                        this.$notify({
                            title: 'Deleted!',
                            message: response.data.message,
                            type: 'success'
                        });
                        this.fetchMachines()
                    })
            },

            viewMachine(item) {
                this.setErrors([])
                this.edit = 'View'
                this.formDialogVisible = true
                this.form = item
                this.form.status = item.status === 'Active' ? '1' : '0'

            },

            openEditDialog(item) {
                this.setErrors([])
                this.edit = true
                this.formDialogVisible = true
                this.form = cloneDeep(item)
            },

            clearForm() {
                if (this.$refs.form) {
                    this.$refs.form.clearValidate()
                }

                this.form = this.getDefaultFieldValues()
                this.formDialogVisible = false
            },

            getDefaultFieldValues() {
                return {
                    name: null,
                    serial_no: null,
                    location: null,
                    status: true,
                    supplier: null,
                    model: null,
                    machine_target: null,
                    parameter_1: null,
                    parameter_2: null,
                    parameter_3: null,
                    parameter_4: null,
                    parameter_5: null,
                    parameter_6: null,
                    parameter_7: null,
                    parameter_8: null,
                    parameter_9: null,
                    parameter_10: null
                }
            }
        },
    }
</script>
