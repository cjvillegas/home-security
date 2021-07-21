<template>
<div>
    <el-row>
        <el-col :span="12">
            <a class="btn btn-success" @click="formDialogVisible = true">
                Add Machine Counter
            </a>
        </el-col>
    </el-row>

    <el-dialog
    :visible.sync="formDialogVisible"
    width="500px">
        <span slot="title" v-show="!edit"> Add Machine Counter </span>
        <span slot="title" v-show="edit">Edit</span>

        <el-form :inline="true" ref="form" :model="form">
            <div class="row">
                <div class="col-md-4">
                    <label>Machine</label>
                </div>
                <div class="col-md-8">
                     <el-select v-model="form.machine_id">
                        <el-option
                        v-for="(machine, machineKey) in machines"
                        :key="machineKey"
                        :label="machine.name"
                        :value="machine.id">

                        </el-option>
                    </el-select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Employees</label>
                </div>
                <div class="col-md-8">
                     <el-select v-model="form.employee_id">
                       <el-option
                        v-for="(employee, employeeKey) in employees"
                        :key="employeeKey"
                        :label="employee.name"
                        :value="employee.id">

                        </el-option>
                    </el-select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Shift</label>
                </div>
                <div class="col-md-8">
                     <el-select v-model="form.shift_id" @change="selectShift()">
                        <el-option
                        v-for="(shift, shiftKey) in shifts"
                        :key="shiftKey"
                        :selected="shift.isSelected"
                        :label="shift.name"
                        :value="shift.id">

                        </el-option>
                    </el-select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Start Counter</label>
                </div>

                <div class="col-md-8">
                    <el-input
                    :model="form.start_counter"
                    >
                    </el-input>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Start Date Time</label>
                </div>

                <div class="col-md-8">
                    <el-date-picker
                    v-model="form.start_counter_time"
                    type="datetime"
                    placeholder="Select date and time"
                    >
                    </el-date-picker>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Stop Counter</label>
                </div>

                <div class="col-md-8">
                    <el-input
                    :model="form.stop_counter"
                    >
                    </el-input>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Stop Date Time</label>
                </div>

                <div class="col-md-8">
                    <el-date-picker
                    v-model="form.stop_counter_time"
                    type="datetime"
                    placeholder="Select date and time"
                    >
                    </el-date-picker>
                </div>
            </div>
        </el-form>
        <span slot="footer" class="dialog-footer">
            <el-button @click="formDialogVisible = false">Cancel</el-button>
            <el-button type="primary" @click="saveMachineCounter()" v-show="!edit">Save</el-button>
            <el-button type="primary" v-show="edit">Update</el-button>
        </span>
    </el-dialog>

    <el-card class="card">

    </el-card>
</div>

</template>

<script>
export default {
    data() {
        return {
            formDialogVisible: false,
            edit: false,
            form: {
                start_counter_time: ''
            },

            machines: [],
            shifts: [
                { id: '1', name: 'Shift 1', start: '06:00', stop: '14:00' },
                { id: '2', name: 'Shift 2', start: '14:00', stop: '22:00' },
                { id: '3', name: 'Shift 3', start: '22:00', stop: '06:00' }
            ],
            employees: [],
            teams: [],
            machineCounters: []
        }
    },

    methods: {
        fetchMachineCounters() {
            let apiUrl = `/admin/machine-counters/list`

            axios.get(apiUrl).then( (response) => {
                console.log(response)
                this.machineCounters = response.data.machineCounters
            })
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

        // fetchShifts() {
        //     let apiUrl = `/admin/shifts/list`

        //     axios.get(apiUrl).then( (response) => {
        //         this.shifts = response.data.shifts
        //     })
        // },

        fetchEmployees() {
            let apiUrl = `/admin/employees/list`

            axios.get(apiUrl).then( (response) => {
                this.employees = response.data.employees
            })
        },


        selectShift() {
            console.log(this.form)
            switch(this.form.shift_id) {
                case "1":
                    this.form.start_counter_time = new Date().toISOString().slice(0,10) + ' ' + '06:00'
                    this.form.stop_counter_time = new Date().toISOString().slice(0,10) + ' ' + '14:00'
                    break
                case "2":
                    this.form.start_counter_time = new Date().toISOString().slice(0,10) + ' ' + '14:00'
                    this.form.stop_counter_time = new Date().toISOString().slice(0,10) + ' ' + '22:00'
                    break
                case "3":
                    this.form.start_counter_time = new Date().toISOString().slice(0,10) + ' ' + '22:00'
                    this.form.stop_counter_time = new Date().toISOString().slice(0,10) + ' ' + '06:00'
                    break
            }
        },

        saveMachineCounter() {
            let apiUrl = `/admin/machine-counters/store`

            axios.post(apiUrl, this.form)
            .then( (response) => {
                console.log(response)
            })
        }
    },

    mounted() {
        this.fetchMachineCounters();
        this.fetchMachines();
        //this.fetchShifts();
        this.fetchEmployees();
    }
}
</script>

<style scoped>
    .el-input, .el-select {
        width: 200px !important;
    }
</style>
