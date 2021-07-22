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
        <span slot="title" v-show="!edit"> Add New Machine </span>
        <span slot="title" v-show="edit">Edit Machine</span>
        <el-form ref="form" :model="form">
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
                <el-select v-model="form.status" placeholder="Status">
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

        <span slot="footer" class="dialog-footer">
            <el-button @click="formDialogVisible = false">Cancel</el-button>
            <el-button type="primary" @click="saveMachine()" v-show="!edit">Save</el-button>
            <el-button type="primary" @click="updateMachine()" v-show="edit">Update</el-button>
        </span>
    </el-dialog>



    <div class="card">
        <el-table
        :data="machines"
        style="width: 100%">
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
    </div>
</div>

</template>

<style scoped>

</style>

<script>
export default {
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
            formDialogVisible: false
        }
    },

    methods: {
        addNew() {
            if (this.edit) {
                this.clearForm()
            }
            this.edit = false
        },
        fetchMachines() {
            this.$API.Machine.fetch()
            .then ( (response) => {
                this.machines = response.data.machines
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
                        Swal.fire(
                            'Success!',
                            response.data.message,
                            'success'
                        ).then(() => {
                            this.fetchMachines()
                        })
                }
            })
        },

        updateMachine() {
            this.formDialogVisible = false
            Swal.fire({
                title: 'Confirm Edit Machine',
                text: 'You are about to edit this Machine Information',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, update it.'
            }).then( async(result) => {
                if (result.isConfirmed) {
                    let apiUrl = `/admin/machines/${this.form.id}/update`
                    axios.patch(apiUrl, this.form)
                    .then( (response) => {
                        Swal.fire(
                            'Updated',
                            response.data.message,
                            'success'
                        ).then( () => {
                            this.fetchMachines();
                        })
                    })
                }else {
                    this.formDialogVisible = true
                }
            })
        },

        deleteMachine(id) {
             Swal.fire({
                title: 'Confirm Delete',
                text: 'You are about to delete this Counter',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it.'
            }).then( async(result) => {
                if(result.isConfirmed) {
                    let apiUrl = `/admin/machines/${id}/destroy`

                    axios.delete(apiUrl)
                    .then( (response) => {
                        Swal.fire(
                            'Deleted',
                            response.data.message,
                            'success'
                        ).then( () => {
                            this.fetchMachines();
                        })
                    })
                }
            })
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

    mounted() {
        this.fetchMachines()
    }
}
</script>

<style scoped>
    .el-input, .el-select {
        width: 320px !important;
    }
</style>
