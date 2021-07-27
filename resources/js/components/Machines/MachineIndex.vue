<template>
    <div>
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" @click="formDialogVisible = true, addNew()">
                    Add New
                </a>
            </div>
        </div>

        <el-dialog
            :visible.sync="formDialogVisible"
            width="20%">
                <span
                    slot="title"
                    v-show="!edit">
                    Add New Machine
                </span>
                <span
                    slot="title"
                    v-show="edit">
                    Edit Machine
                </span>
                <el-form
                    ref="form"
                    :model="form">
                        <el-form-item>
                            <el-input
                            placeholder="Machine Name"
                            v-model="form.name"
                            clearable>
                            </el-input>
                        </el-form-item>

                        <el-form-item>
                            <el-input
                            placeholder="Serial No."
                            v-model="form.serial_no"
                            clearable>
                            </el-input>
                        </el-form-item>

                        <el-form-item>
                            <el-input
                                placeholder="Location"
                                v-model="form.location"
                                clearable>
                            </el-input>
                        </el-form-item>

                        <el-form-item cenetered>
                            <el-select
                                v-model="form.status"
                                placeholder="Status">
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
            <el-table
                :data="machines"
                class="w-100"
                fit>
                    <el-table-column
                        prop="name"
                        label="Name">
                    </el-table-column>
                    <el-table-column
                        prop="serial_no"
                        label="Serial No.">
                    </el-table-column>
                    <el-table-column
                        prop="location"
                        label="Location">
                    </el-table-column>
                    <el-table-column
                        prop="status"
                        label="Status">
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
                                        class="text-secondary"
                                        type="text">
                                        <i class="fas fa-pen"></i>
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="Delete"
                                    placement="top"
                                    :open-delay="1000">
                                    <el-button
                                        @click="deleteMachine(scope.row.id)"
                                        type="text">
                                        <i class="fas fa-trash-alt text-red-500"></i>
                                    </el-button>
                                </el-tooltip>
                            </template>
                        </template>
                    </el-table-column>
            </el-table>

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
                filters: {}
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
            },
            fetchMachines() {
                this.$API.Machine.fetch(this.filters)
                .then ( (response) => {
                    this.machines = response.data.machines.data
                    this.filters.total = response.data.machines.total
                })
                .catch(err => {
                    console.log(err)
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
                this.$confirm('You are about to delete this Machine', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                }).then( () => {
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
                }).catch( () => {})
            },

            openEditDialog(item) {
                this.edit = true,
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
    .el-input, .el-select {
        width: 320px !important;
    }
</style>
