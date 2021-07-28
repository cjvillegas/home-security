<template>
    <div>
        <el-dialog
            :visible.sync="formDialogVisible"
            :title="dialogTitle"
            width="40%">
                <el-form
                    ref="form"
                    :model="form">
                        <el-form-item
                            label="Machine Name"
                            prop="name">
                            <el-input
                                v-model="form.name"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>

                        <el-form-item
                            label="Serial No."
                            prop="serial_no">
                            <el-input
                                v-model="form.serial_no"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>

                        <el-form-item
                            label="Location"
                            prop="location">
                            <el-input
                                placeholder="Location"
                                v-model="form.location"
                                clearable
                                class="w-100">
                            </el-input>
                        </el-form-item>

                        <el-form-item
                            label="Status"
                            prop="status">
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
                </el-form>

                <span
                    slot="footer"
                    class="dialog-footer">
                        <el-button
                            @click="formDialogVisible = false">
                            Cancel
                        </el-button>
                        <el-button
                            type="primary"
                            @click="saveMachine()"
                            v-show="!edit">
                            Save
                        </el-button>
                        <el-button
                            type="primary"
                            @click="updateMachine()"
                            v-show="edit">
                            Update
                        </el-button>
                </span>
        </el-dialog>

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
                            @click="formDialogVisible = true, addNew()">
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
    </div>

</template>

<script>
    import pagination from '../../mixins/pagination'
    export default {
        mixins: [pagination],
        data() {
            return {
                edit: false,
                machines: [],
                form: {
                    name: '',
                    serial_no: '',
                    location: '',
                    status: '',
                },
                formDialogVisible: false,
                filters: {
                    searchString: ''
                },
                loading:false,
                dialogTitle: ''
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
                this.edit = false
                this.dialogTitle = 'Add Machine'
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
            saveMachine() {
                this.$API.Machine.save(
                    this.form
                ).then( (response) => {
                     switch(response.status){
                        case 200:
                            this.formDialogVisible = false
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
                    console.log(err)
                })
            },
            updateMachine() {
                this.formDialogVisible = false

                this.$confirm('You are about to edit this Machine. Continue?', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    type: 'info'
                }).then( () => {
                    let apiUrl = `/admin/machines/${this.form.id}/update`
                    axios.patch(apiUrl, this.form)
                    .then( (response) => {
                        this.$notify({
                            title: 'Success!',
                            message: response.data.message,
                            type: 'success'
                        });

                        this.fetchMachines()
                    })
                }).catch( () => {
                    this.formDialogVisible = true
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
                this.edit = true,
                this.dialogTitle = 'Edit Machine'
                this.formDialogVisible = true
                this.form.id = item.id
                this.form.name = item.name
                this.form.serial_no = item.serial_no
                this.form.location = item.location
                this.form.status = item.status
            },

            clearForm() {
                this.form.name = ''
                this.form.serial_no = ''
                this.form.location = ''
                this.form.status = ''
            },
        },
    }
</script>

<style scoped>
    /* .el-input, .el-select {
        width: 320px !important;
    } */
</style>
