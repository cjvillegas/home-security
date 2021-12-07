<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Employee List</h4>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search employees..."
                        @keyup.enter.native.prevent="getList"
                        style="width: 250px">
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
                            @click="getList"
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
                        @click="stageEmployee(null)">
                        <i class="fas fa-plus"></i> Add Employee
                    </el-button>
                </div>
            </div>

            <el-table
                fit
                :data="employees">
                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop">
                    <template slot-scope="scope">
                        <template v-if="column.prop === 'id'">
                            {{ scope.row[column.prop] | numFormat }}
                        </template>
                        <template v-else-if="column.prop === 'fullname'">
                            {{ scope.row[column.prop] | ucWords }}
                        </template>
                        <template v-else-if="column.prop === 'clock_num'">
                            {{ scope.row[column.prop] | numFormat }}
                        </template>
                        <template v-else-if="column.prop === 'team'">
                            {{ scope.row.team ? scope.row.team.name : '' | ucWords }}
                        </template>
                        <template v-else-if="column.prop === 'shift'">
                            {{ scope.row.shift ? scope.row.shift.name : '' | ucWords }}
                        </template>
                        <template v-else>
                            {{ scope.row[column.prop] }}
                        </template>
                    </template>
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
                    label="Actions"
                    width="200">
                    <template slot-scope="scope">
                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="View Employee Information"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                @click="viewEmployee(scope.row)"
                                type="text"
                                class="ml-2 text-secondary">
                                <i class="fas fa-eye"></i>
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Print Employee Barcode"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                @click="printBarcode(scope.row)"
                                type="text"
                                class="ml-2">
                                <i class="fas fa-print"></i>
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Update Employee"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                @click="stageEmployee(scope.row)"
                                type="text"
                                class="ml-2">
                                <i class="fas fa-pencil-alt"></i>
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            class="item"
                            effect="dark"
                            :content="`${scope.row.is_active ? 'Deactivate' : 'Activate'} Employee`"
                            :open-delay="500"
                            placement="top">
                            <el-popconfirm
                                @confirm="changeStatus(scope.row)"
                                confirm-button-text='OK'
                                cancel-button-text='No, Thanks'
                                icon="el-icon-info"
                                icon-color="red"
                                :title="`Are you sure to ${scope.row.is_active ? 'deactivate' : 'activate'} this employee?`">
                                <el-button
                                    type="text"
                                    class="text-success ml-2"
                                    slot="reference">
                                    <i
                                        v-if="scope.row.is_active"
                                        class="fas fa-user-alt-slash">
                                    </i>
                                    <i
                                        v-else
                                        class="fas fa-user-plus">
                                    </i>
                                </el-button>
                            </el-popconfirm>
                        </el-tooltip>

                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Delete Employee"
                            :open-delay="500"
                            placement="top">
                            <el-popconfirm
                                @confirm="deleteEmployee(scope.row)"
                                confirm-button-text='OK'
                                cancel-button-text='No, Thanks'
                                icon="el-icon-info"
                                icon-color="red"
                                title="Are you sure to delete this employee?">
                                <el-button
                                    type="text"
                                    class="text-danger ml-2"
                                    slot="reference">
                                    <i class="fas fa-trash-alt"></i>
                                </el-button>
                            </el-popconfirm>
                        </el-tooltip>
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

        <employee-form
            :model="model"
            :visible.sync="showForm"
            :shifts="pageData.shifts"
            :teams="pageData.teams"
            @close="closeForm">
        </employee-form>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from '../../mixins/pagination'

    export default {
        name: "EmployeeList",
        mixins: [pagination],
        props: {
            pageData: {
                type: Object,
                required: true
            }
        },
        data() {
            let columns = [
                {label: 'ID', prop: 'id', showOverflowTooltip: true, sortable: true},
                {label: 'Full Name', prop: 'fullname', showOverflowTooltip: true, sortable: true},
                {label: 'Barcode', prop: 'barcode', showOverflowTooltip: true, sortable: true},
                {label: 'Pin Code', prop: 'pin_code', showOverflowTooltip: true, sortable: true},
                {label: 'Shift Target', prop: 'target', showOverflowTooltip: true, sortable: true},
                {label: 'Working Hours', prop: 'standard_working_hours', showOverflowTooltip: true, sortable: true},
                {label: 'Clock No.', prop: 'clock_num', showOverflowTooltip: true, sortable: true},
                {label: 'Team', prop: 'team', showOverflowTooltip: true, sortable: true},
                {label: 'Shift', prop: 'shift', showOverflowTooltip: true, sortable: true},
            ]

            return {
                loading: false,
                showForm: false,
                model: null,
                filters: {
                    searchString: null,
                    status: 1
                },
                columns: cloneDeep(columns),
                employees: []
            }
        },
        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'

            this.getList()

            this.$EventBus.listen('EMPLOYEE_CREATE', _ => {
                this.getList()
            })

            this.$EventBus.listen('EMPLOYEE_UPDATE', _ => {
                this.getList()
            })
        },
        methods: {
            getList() {
                this.loading = true

                let params = {...this.filters, ...this.pagination}

                this.$API.Employee.getList(params)
                    .then(res => {
                        this.employees = res.data.data
                        this.pagination.total = res.data.total
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            viewEmployee(employee) {
                this.$router.push({name: 'Employee View', params: {id: employee.id}})
            },
            deleteEmployee(employee) {
               this.loading = true

                this.$API.Employee.delete(employee.id)
                    .then(res => {
                        if (res.data) {
                            this.getList()
                        }
                    })
                    .catch(err => {
                        console.log(err)
                        this.loading = false
                    })
            },
            changeStatus(employee) {
                let status = employee.is_active ? 'Deactivate' : 'Activate'

                this.loading = true

                this.$API.Employee.changeStatus(employee.id)
                    .then(res => {
                        if (res.data) {

                            this.getList()
                            this.$notify({
                                title: 'Success',
                                message: `Employee successfully ${status}.`,
                                type: 'success'
                            })
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            stageEmployee(employee) {
                this.model = cloneDeep(employee)
                this.showForm = true
            },
            closeForm() {
                this.model = null
                this.showForm = false
            },
            printBarcode(employee) {
                let baseUrl = window.location.origin

                let content = "<html><head>"
                content += `<link href="${baseUrl}/css/print.css" rel="stylesheet" />`
                content += `<style media="print">
                    @page
                    {
                        margin: 0mm;  /* this affects the margin in the printer settings */
                        size: 89mm 28mm;
                    }
                </style>`
                content += "<body class='text-center'>"
                content += `<div class="text-uppercase f-size-14">${employee.fullname}</div>`
                content += `<svg id="barcode"></svg>`
                content += "</body></head></html>"

                let script = document.createElement("script")
                script.type = "text/javascript"
                script.src = `${baseUrl}/js/jsbarcode.code128.min.js`

                let anotherScript = document.createElement("script")
                anotherScript.text += `setTimeout(_ => {
                    JsBarcode("#barcode", "${employee.barcode}", {
                        height: 35,
                        fontSize: 14
                    })
                }, 200)`

                let win = window.open("")
                win.document.write(content)
                win.document.body.appendChild(script)
                win.document.body.appendChild(anotherScript)
                win.document.close()
                setTimeout(_ => {
                    win.print()
                }, 300)
            }
        }
    }
</script>
