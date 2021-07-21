<template>
<div>
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" @click="formDialogVisible = true, edit = false, clearForm()">
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
            label="Action">
            <template slot-scope="scope">
                <el-button class="btn-link">
                    <i class="el-icon-view"></i>
                </el-button>
                <el-button class="btn-link" @click="openEditDialog(scope.row)">
                    <i class="el-icon-edit-outline"></i>
                </el-button>
                <el-button class="btn-link">
                    <i class="el-icon-delete"></i>
                </el-button>
            </template>
            </el-table-column>
        </el-table>

        <el-pagination>

        </el-pagination>
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
                    console.log(this.form)
                    this.formDialogVisible = true
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
