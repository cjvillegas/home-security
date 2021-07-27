<template>
    <div>
        <el-row style="margin-bottom: 10px;">
            <el-col :span="12">
                <a class="btn btn-success" @click="formDialogVisible = true, addNew()">
                    Add Machine Counter
                </a>
            </el-col>
        </el-row>

        <el-dialog
            :visible.sync="formDialogVisible"
            width="500px">
                <span
                    slot="title"
                    v-show="!edit">
                        Add Machine Counter
                </span>
                <span
                    slot="title"
                    v-show="edit">
                        Edit Machine Counter
                </span>

                <el-form
                    :inline="true"
                    ref="form"
                    v-model="form">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Machine</label>
                            </div>
                            <div class="col-md-8">
                                <el-select v-model="form.machine_id">
                                    <el-option
                                        v-for="machine in machines"
                                        :key="machine.id"
                                        :label="machine.name"
                                        :value="machine.id">
                                    </el-option>
                                </el-select>
                            </div>
                        </div>

                        <!-- <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Employees</label>
                            </div>
                            <div class="col-md-8">
                                <el-select v-model="form.employee_id">
                                <el-option
                                    v-for="employee in employees"
                                    :key="employee.id"
                                    :label="employee.fullname"
                                    :value="employee.id">

                                    </el-option>
                                </el-select>
                            </div>
                        </div> -->

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Employee</label>
                            </div>
                            <div class="col-md-8">
                                <el-autocomplete
                                    v-model="employee_name"
                                    :fetch-suggestions="querySearch"
                                    placeholder="Employee Name"
                                    value-key="fullname"
                                    @select="selectItem">
                                </el-autocomplete>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Shift</label>
                            </div>
                            <div class="col-md-8">
                                <el-select v-model="form.shift_id" @change="selectShift()">
                                    <el-option
                                        v-for="shift in shifts"
                                        :key="shift.id"
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
                                    v-model="form.start_counter"
                                    placeholder="Digits only (Ex. 1234)"
                                    clearable>
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
                                    placeholder="Select date and time">
                                </el-date-picker>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Stop Counter</label>
                            </div>

                            <div class="col-md-8">
                                <el-input
                                    v-model="form.stop_counter"
                                    placeholder="Digits only (Ex. 1234)"
                                    clearable>
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
                                        placeholder="Select date and time">
                                </el-date-picker>
                            </div>
                        </div>
                </el-form>
                <span
                    slot="footer"
                    class="dialog-footer">
                        <el-button @click="formDialogVisible = false">
                            Cancel
                        </el-button>
                        <el-button
                            type="primary"
                            @click="saveMachineCounter()"
                            v-show="!edit">
                            Save
                        </el-button>
                        <el-button
                            type="primary"
                            @click="updateMachineCounter()"
                            v-show="edit">
                            Update
                        </el-button>
                </span>
        </el-dialog>

        <el-card class="card">
            <el-table
                :data="machineCounters"
                style="width: 100%">
                    <el-table-column
                        prop="machine.name"
                        label="Machine Name">
                    </el-table-column>
                    <el-table-column
                        prop="employee.fullname"
                        label="Employee Name">
                    </el-table-column>
                    <el-table-column
                        prop="shift.name"
                        label="Shift Name">
                    </el-table-column>
                    <el-table-column
                        prop="start_counter"
                        label="Start Counter">
                    </el-table-column>
                    <el-table-column
                        prop="start_counter_time"
                        label="Start Counter Time">
                    </el-table-column>
                    <el-table-column
                        prop="stop_counter"
                        label="Stop Counter">
                    </el-table-column>
                    <el-table-column
                        prop="stop_counter_time"
                        label="Stop Counter Time">
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
                                        @click="editMachineCounter(scope.row), formDialogVisible = true"
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
                                        @click="deleteMachineCounter(scope.row.id)"
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
                formDialogVisible: false,
                edit: false,
                form: {
                    machine_id: '',
                    employee_id: '',
                    shift_id: '',
                    start_counter: '',
                    stop_counter: '',
                    start_counter_time: '',
                    stop_counter_time: ''
                },
                machines: [],
                shifts: [
                    { id: '1', name: 'Shift 1', start: '06:00', stop: '14:00' },
                    { id: '2', name: 'Shift 2', start: '14:00', stop: '22:00' },
                    { id: '3', name: 'Shift 3', start: '22:00', stop: '06:00' }
                ],
                employees: [],
                employee_name: '',
                teams: [],
                machineCounters: [],
                filters: {}
            }
        },

        created() {
            this.filters.size = 10
            this.functionName = 'fetchMachineCounters'
        },

        mounted() {
            this.fetchMachineCounters()
        },

        methods: {
            addNew() {
                if (this.edit) {
                    this.clearForm()
                }

                this.edit = false
            },

            fetchMachineCounters() {
                let apiUrl = `/admin/machine-counters/list?page=${this.filters.page}&size=${this.filters.size}`

                axios.get(apiUrl).then( (response) => {
                    this.machines = response.data.machines
                    this.machineCounters = response.data.machineCounters.data
                    this.employees = response.data.employees
                    this.filters.total = response.data.machineCounters.total
                })
            },

            selectShift() {
                switch(this.form.shift_id) {
                    case "1":
                        this.form.start_counter_time = moment().set('hour', '06').set('minute', '00').format('YYYY-MM-DD HH:mm')
                        this.form.stop_counter_time = moment().set('hour', '14').set('minute', '00').format('YYYY-MM-DD HH:mm')
                        break
                    case "2":
                        this.form.start_counter_time = moment().set('hour', '14').set('minute', '00').format('YYYY-MM-DD HH:mm')
                        this.form.stop_counter_time = moment().set('hour', '22').set('minute', '00').format('YYYY-MM-DD HH:mm')
                        break
                    case "3":
                        this.form.start_counter_time = moment().set('hour', '22').set('minute', '00').format('YYYY-MM-DD HH:mm')
                        this.form.stop_counter_time = moment().add(1, 'days').set('hour', '06').set('minute', '00').format('YYYY-MM-DD HH:mm')
                        break
                }
            },

            saveMachineCounter() {
                let apiUrl = `/admin/machine-counters/store`

                axios.post(apiUrl, this.form)
                .then( (response) => {
                    switch(response.status){
                        case 200:
                            this.formDialogVisible = false
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchMachineCounters()
                    }
                }).catch( err => {})
            },

            updateMachineCounter() {
                this.formDialogVisible = false
                this.$confirm('You are about to edit this Machine Counter. Continue?', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    type: 'info'
                })
                    .then( () => {
                        let apiUrl = `/admin/machine-counters/${this.form.id}/update`
                        axios.patch(apiUrl, this.form)
                        .then( (response) => {
                            this.$notify({
                                title: 'Success!',
                                message: response.data.message,
                                type: 'success'
                            });

                            this.fetchMachineCounters()
                        })
                    })
                    .catch( () => {
                        this.formDialogVisible = true
                    })
            },

            editMachineCounter(item) {
                this.edit = true
                this.form.id = item.id
                this.form.machine_id = item.machine_id
                this.form.employee_id = item.employee_id
                this.employee_name = item.employee.fullname
                this.form.shift_id = item.shift_id
                this.form.start_counter = item.start_counter
                this.form.start_counter_time = item.start_counter_time
                this.form.stop_counter = item.stop_counter
                this.form.stop_counter_time = item.stop_counter_time
            },

            deleteMachineCounter(id) {
                this.$confirm('You are about to delete this Machine', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                })
                    .then( () => {
                        let apiUrl = `/admin/machine-counters/${id}/destroy`
                        axios.delete(apiUrl)
                        .then( (response) => {
                            this.$notify({
                                title: 'Deleted!',
                                message: response.data.message,
                                type: 'success'
                            });
                            this.fetchMachineCounters()
                        })
                    })
                    .catch( () => {})
            },

            querySearch(queryString, cb) {
                let apiUrl = `/admin/employees/search`
                var employees = []

                axios.post(apiUrl, {searchString: queryString})
                .then( (response) => {
                    employees = response.data.employees

                    var results = queryString ? employees.filter(this.createFilter(queryString)) : employees
                    console.log(results)
                    cb(results)
                })
            },

            createFilter(queryString) {
                return (employee) => {
                    return (employee.fullname.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                };
            },

            selectItem(item) {
                this.form.employee_id = item.id
                console.log(this.form.employee_id)
            },

            clearForm() {
                this.employee_name = ''
                this.form = {
                    machine_id: '',
                    employee_id: '',
                    shift_id: '',
                    start_counter: '',
                    stop_counter: '',
                    start_counter_time: '',
                    stop_counter_time: ''
                }
            }
        },
    }
</script>

<style scoped>
    .el-input, .el-select, .el-autocomplete {
        width: 200px !important;
    }
</style>
