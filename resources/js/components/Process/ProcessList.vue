<template>
    <div>
        <global-page-header title="Process"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search processes..."
                        @keyup.enter.native.prevent="applySearch"
                        style="width: 250px">
                    </el-input>
                </div>

                <div class="ml-auto">
                    <el-button
                        type="primary"
                        @click="stageProcess(null)">
                        <i class="fas fa-plus"></i> Add Process
                    </el-button>
                </div>
            </div>

            <el-table
                fit
                :data="processes"
                class="mt-3">
                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop"
                    :width="column.width ? column.width : ''">
                    <template slot-scope="scope">
                        <template v-if="['name'].includes(column.prop)">
                            {{ scope.row[column.prop] | ucWords}}
                        </template>
                        <template v-else-if="'stop_start_button_required' === column.prop">
                            <el-tag
                                :type="scope.row.stop_start_button_required ? 'success' : 'info'"
                                effect="dark">
                                {{ scope.row.stop_start_button_required ? 'Yes' : 'No' }}
                            </el-tag>
                        </template>
                        <template v-else-if="'categories' === column.prop">
                            <el-tag
                                v-if="scope.row.process_categories && !!scope.row.process_categories.length"
                                v-for="category in scope.row.process_categories"
                                :key="category.id"
                                type="primary"
                                effect="dark"
                                class="m-1">
                                {{ category.name | ucWords }}
                            </el-tag>
                        </template>
                        <template v-else>
                            {{ scope.row[column.prop] }}
                        </template>
                    </template>
                </el-table-column>

                <el-table-column
                    label="Actions">
                    <template slot-scope="scope">
                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="View Process Information"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                @click="viewProcess(scope.row)"
                                type="text"
                                class="ml-2 text-secondary">
                                <i class="fas fa-eye"></i>
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Update Process"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                @click="stageProcess(scope.row)"
                                type="text"
                                class="ml-2">
                                <i class="fas fa-pencil-alt"></i>
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Print Process Barcode"
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
                            content="Delete Process"
                            :open-delay="500"
                            placement="top">
                            <el-popconfirm
                                @confirm="deleteProcess(scope.row)"
                                confirm-button-text='OK'
                                cancel-button-text='No, Thanks'
                                icon="el-icon-info"
                                icon-color="red"
                                title="Are you sure to delete this process?">
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

        <process-form
            :model="model"
            :process-categories="processCategories"
            :visible.sync="showForm"
            @close="closeForm">
        </process-form>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from "../../mixins/pagination";

    export default {
        name: "ProcessList",

        mixins: [pagination],

        data() {
            let columns = [
                {label: 'ID', prop: 'id', showOverflowTooltip: true, sortable: true, width: '80'},
                {label: 'Name', prop: 'name', showOverflowTooltip: true, sortable: true},
                {label: 'Barcode', prop: 'barcode', showOverflowTooltip: true, sortable: true},
                {label: 'Categories', prop: 'categories', showOverflowTooltip: false, sortable: false},
                {label: 'Trade Target', prop: 'trade_target', showOverflowTooltip: true, sortable: true},
                {label: 'Internet Target', prop: 'internet_target', showOverflowTooltip: true, sortable: true},
                {label: 'Trade Target New Joiner', prop: 'trade_target_new_joiner', showOverflowTooltip: true, sortable: true},
                {label: 'Internet Target New Joiner', prop: 'internet_target_new_joiner', showOverflowTooltip: true, sortable: true},
                {label: 'Process Manufacturing Time', prop: 'process_manufacturing_time', showOverflowTooltip: true, sortable: true},
                {label: 'Stop Start Button Required', prop: 'stop_start_button_required', showOverflowTooltip: true, sortable: true},
            ]

            return {
                loading: false,
                showForm: false,
                filters: {
                    searchString: null
                },
                columns: columns,
                processes: [],
                processCategories: [],
                model: null
            }
        },

        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'

            this.getList()

            this.getProcessCategories()

            this.$EventBus.listen('PROCESS_CREATE', _ => {
                this.getList()
            })

            this.$EventBus.listen('PROCESS_UPDATE', _ => {
                this.getList()
            })
        },

        methods: {
            viewProcess(process) {
                this.$router.push({name: 'Process View', params: {id: process.id}})
            },

            stageProcess(process) {
                this.model = cloneDeep(process)
                this.showForm = true
            },

            closeForm() {
                this.model = null
                this.showForm = false
            },

            deleteProcess(process) {
                this.loading = true

                this.$API.Processes.delete(process.id)
                    .then(res => {
                        if (res.data) {
                            this.getList()
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            printBarcode(process) {
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
                content += `<div class="text-uppercase f-size-14">${process.name}</div>`
                content += `<svg id="barcode"></svg>`
                content += "</body></head></html>"

                let script = document.createElement("script")
                script.type = "text/javascript"
                script.src = `${baseUrl}/js/jsbarcode.code128.min.js`

                let anotherScript = document.createElement("script")
                anotherScript.text += `setTimeout(_ => {
                    JsBarcode("#barcode", "${process.barcode}", {
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
            },

            applySearch() {
                this.pagination.page = 1

                this.getList()
            },

            getList() {
                this.loading = true

                let params = {...this.filters, ...this.pagination}

                this.$API.Processes.getList(params)
                    .then(res => {
                        console.log(res.data)
                        this.processes = res.data.data
                        this.pagination.total = res.data.total
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            getProcessCategories() {
                this.$API.ProcessCategory.getAll()
                .then(res => {
                    if (res.data) {
                        this.processCategories = res.data
                    }
                })
            },
        }
    }
</script>
