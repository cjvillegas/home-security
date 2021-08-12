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
                            prop="status"
                            label="Status"
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
                :total="filters.total"
                :page-size="filters.size"
                :page-sizes="[10, 25, 50, 100]"
                :current-page="filters.page"
                @size-change="handleSize"
                @current-change="handlePage">
            </el-pagination>
        </el-card>

        <el-dialog
            :visible.sync="formDialogVisible"
            :title="edit ? 'Edit Machine' : 'Add Machine'"
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
                                v-model="form.location"
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
                                class="w-100">
                                <el-option
                                    label="Active"
                                    value=1>
                                </el-option>

                                <el-option
                                    label="Inactive"
                                    value=0>
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
                                placeholder="Supplier"
                                v-model="form.supplier"
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
                                placeholder="Model"
                                v-model="form.model"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 1"
                            prop="parameter1"
                            :error="hasError('parameter1')">
                            <el-input
                                v-model="form.parameter1"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 2"
                            prop="parameter2"
                            :error="hasError('parameter2')">
                            <el-input
                                v-model="form.parameter2"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 3"
                            prop="parameter3"
                            :error="hasError('parameter3')">
                            <el-input
                                v-model="form.parameter3"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 4"
                            prop="parameter4"
                            :error="hasError('parameter4')">
                            <el-input
                                v-model="form.parameter4"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 5"
                            prop="parameter5"
                            :error="hasError('parameter5')">
                            <el-input
                                v-model="form.parameter5"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 6"
                            prop="parameter6"
                            :error="hasError('parameter6')">
                            <el-input
                                v-model="form.parameter6"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 7"
                            prop="parameter7"
                            :error="hasError('parameter7')">
                            <el-input
                                v-model="form.parameter7"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 8"
                            prop="parameter8"
                            :error="hasError('parameter8')">
                            <el-input
                                v-model="form.parameter8"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 9"
                            prop="parameter9"
                            :error="hasError('parameter9')">
                            <el-input
                                v-model="form.parameter9"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Parameter 10"
                            prop="parameter10"
                            :error="hasError('parameter10')">
                            <el-input
                                v-model="form.parameter10"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>
                    </el-col>
                    {{ form.parameter9 }}
                </el-row>
            </el-form>

            <span
                slot="footer"
                class="dialog-footer">
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
                loading: false
            }
        },

        created() {
            this.filters.size = 10
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
                this.$API.Machine.fetch(this.filters)
                .then ( (response) => {
                    this.machines = response.data.machines.data
                    this.filters.total = response.data.machines.total
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

            openEditDialog(item) {
                this.setErrors([])
                this.edit = true
                this.formDialogVisible = true
                this.form.id = item.id
                this.form.name = item.name
                this.form.serial_no = item.serial_no
                this.form.location = item.location
                this.form.status = item.status
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
                    status: null,
                    supplier: null,
                    model: null,
                    parameter1: null,
                    parameter2: null,
                    parameter3: null,
                    parameter4: null,
                    parameter5: null,
                    parameter6: null,
                    parameter7: null,
                    parameter8: null,
                    parameter9: null,
                    parameter10: null
                }
            }
        },
    }
</script>
