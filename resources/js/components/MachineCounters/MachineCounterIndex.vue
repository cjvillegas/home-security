<template>
    <div>
        <el-dialog
            :visible.sync="formDialogVisible"
            :title="dialogTitle"
            top="5vh"
            width="40%">
            <el-form
                ref="form"
                v-model="form">
                <el-form-item
                    label="Machine"
                    prop="machine_id"
                    :error="hasError('machine_id')">
                    <el-select v-model="form.machine_id">
                        <el-option
                            v-for="machine in machines"
                            :key="machine.id"
                            :label="machine.name"
                            :value="machine.id">
                        </el-option>
                    </el-select>
                </el-form-item>

                <el-form-item
                    label="Employee"
                    prop="employee_id"
                    :error="hasError('employee_id')">
                    <el-autocomplete
                        v-model="employee_name"
                        :fetch-suggestions="querySearch"
                        placeholder="Employee Name"
                        value-key="fullname"
                        @select="selectItem">
                    </el-autocomplete>
                </el-form-item>

                <el-form-item
                    label="Shift"
                    prop="shift_id"
                    :error="hasError('shift_id')">
                    <el-select v-model="form.shift_id" @change="selectShift()">
                        <el-option
                            v-for="shift in shifts"
                            :key="shift.id"
                            :selected="shift.isSelected"
                            :label="shift.name"
                            :value="shift.id">
                        </el-option>
                    </el-select>
                </el-form-item>

                <el-form-item
                    label="Start Counter"
                    prop="start_counter"
                    :error="hasError('start_counter')">
                    <el-input
                        v-model="form.start_counter"
                        placeholder="Digits only (Ex. 1234)"
                        clearable>
                    </el-input>
                </el-form-item>

                <el-form-item
                    label="Start Date Time"
                    prop="start_counter_time"
                    :error="hasError('start_counter_time')">
                    <el-date-picker
                        v-model="form.start_counter_time"
                        type="datetime"
                        placeholder="Select date and time">
                    </el-date-picker>
                </el-form-item>

                <el-form-item
                    label="Stop Counter"
                    prop="stop_counter"
                    :error="hasError('stop_counter')">
                    <el-input
                        v-model="form.stop_counter"
                        placeholder="Digits only (Ex. 1234)"
                        clearable>
                    </el-input>
                </el-form-item>

                <el-form-item
                    label="Stop Counter Time"
                    prop="stop_counter_time"
                    :error="hasError('stop_counter_time')">
                    <el-date-picker
                            v-model="form.stop_counter_time"
                            type="datetime"
                            placeholder="Select date and time">
                    </el-date-picker>
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
            <div v-loading="loading">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Machine Name..."
                            style="width: 250px">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="formDialogVisible = true, addNew()">
                            <i class="fas fa-plus"></i> Add Machine Counter
                        </el-button>
                    </div>
                </div>
                <el-table
                    :data="machineCounters.filter(data => !filters.searchString || data.machine.name.toLowerCase().includes(filters.searchString.toLowerCase()))"
                    style="width: 100%">
                        <el-table-column
                            prop="machine.name"
                            label="Machine Name"
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="employee.fullname"
                            label="Employee Name"
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="shift.name"
                            label="Shift Name"
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="start_counter"
                            label="Start Counter"
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="start_counter_time"
                            label="Start Counter Time"
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="stop_counter"
                            label="Stop Counter"
                            sortable>
                        </el-table-column>
                        <el-table-column
                            prop="stop_counter_time"
                            label="Stop Counter Time"
                            sortable>
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
                                            type="text">
                                            <i class="fas fa-pen"></i>
                                        </el-button>
                                    </el-tooltip>
                                    <el-popconfirm
                                        @confirm="deleteMachineCounter(scope.row.id)"
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
            </div>
        </el-card>
    </div>
</template>

<script>
    import pagination from '../../mixins/pagination'
    import { formHelper } from '../../mixins/formHelper'

    export default {
        mixins: [pagination, formHelper],
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
                loading: false,
                filters: {
                    searchString: ''
                },
                dialogTitle: ''
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
                this.dialogTitle = 'Add New Machine Counter'
                this.edit = false
            },

            fetchMachineCounters() {
                let apiUrl = `/admin/machine-counters/list`
                this.loading = true
                axios.post(apiUrl, this.filters).then( (response) => {
                    this.loading = false
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
                            this.clearForm()
                    }
                }).catch( err => {
                    if (err.response.status === 422) {
                        this.setErrors(err.response.data.errors)
                    }
                })
            },

            updateMachineCounter() {
                this.$confirm('You are about to edit this Machine Counter. Continue?', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    type: 'info'
                })
                    .then( () => {
                        let apiUrl = `/admin/machine-counters/${this.form.id}/update`
                        this.loading = true
                        axios.patch(apiUrl, this.form)
                        .then( (response) => {
                            this.$notify({
                                title: 'Success!',
                                message: response.data.message,
                                type: 'success'
                            });
                            this.formDialogVisible = false
                            this.fetchMachineCounters()
                        })
                    })
                    .catch( err => {
                        if (err.response.status === 422) {
                            this.setErrors(err.response.data.errors)
                        }
                    })
            },

            editMachineCounter(item) {
                this.edit = true
                this.dialogTitle = 'Update Machine Counter'
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
            },

            querySearch(queryString, cb) {
                let apiUrl = `/admin/employees/search`
                var employees = []

                axios.post(apiUrl, {searchString: queryString})
                .then( (response) => {
                    employees = response.data.employees

                    var results = queryString ? employees.filter(this.createFilter(queryString)) : employees
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
        width: 100% !important;
    }
</style>
