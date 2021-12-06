<template>
    <div>
        <global-page-header title="Orders"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="searchString"
                        clearable
                        placeholder="Type to search orders..."
                        @input="applySearch"
                        style="width: 250px">
                    </el-input>
                </div>

                <div class="ml-auto">
                    <global-filter-box>
                        <label>Select Dates</label>
                        <el-date-picker
                            v-model="filters.filters.dates"
                            type="daterange"
                            clearable
                            placeholder="Pick dates"
                            value-format="yyyy-MM-dd"
                            class="w-100">
                        </el-date-picker>

                        <global-employee-selector
                            class="mt-3"
                            :value.sync="filters.filters.employees"
                            is-multiple>
                        </global-employee-selector>

                        <global-process-selector
                            class="mt-3"
                            :value.sync="filters.filters.processes"
                            is-multiple>
                        </global-process-selector>

                        <el-button
                            @click="applyFilters"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>

                    <el-button
                        @click="manageTableColumns">
                        <i class="fas fa-cogs"></i>
                    </el-button>

                    <el-button
                        @click="exportOrders"
                        type="success">
                        <i class="fas fa-file-export"></i> Export
                    </el-button>
                </div>
            </div>

            <el-table
                fit
                :data="orders"
                height="65vh"
                class="mt-3"
                width="100%"
                ref="ordersTable"
                :cell-style="getCellStyle">
                <el-table-column
                    v-for="col in columns"
                    v-if="!selectedColumns.length || selectedColumns.includes(col.prop)"
                    :key="col.prop"
                    :prop="col.prop"
                    :label="col.label"
                    :fixed="col.isFixed"
                    :min-width="col.width">
                    <template slot-scope="scope">
                        <template v-if="['order_status', 'updated_by'].includes(col.prop)">
                            {{ scope.row['scanner'] ? scope.row['scanner'][col.prop] : '' }}
                        </template>
                        <template v-else-if="['ordered_at', 'work_date', 'last_updated_at'].includes(col.prop)">
                            <template v-if="col.prop === 'last_updated_at'">
                                {{ (scope.row['scanner'] ? scope.row['scanner'].last_updated_at : '') | fixDateByFormat }}
                            </template>
                            <template v-else>
                                {{ scope.row[col.prop] | fixDateByFormat }}
                            </template>
                        </template>
                        <template v-else>
                            {{ scope.row[col.prop] }}
                        </template>
                    </template>
                </el-table-column>

                <el-table-column
                    fixed="right"
                    label="Action"
                    width="60">
                    <template slot-scope="scope">
                        <el-button
                            @click="colorPickerClicked(scope.row)"
                            type="text"
                            icon="el-icon-s-check">
                        </el-button>
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

        <global-column-manager
            :visible.sync="showColumnManager"
            title="Orders Column Manager"
            :columns="columns"
            :type="ColumnManagerTypes.TYPE_ORDER"
            @change="updateSelectedColumns"
            @close="closeColumnManager">
        </global-column-manager>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep"
    import debounce from "lodash/debounce"
    import pagination from '../../mixins/pagination'
    import filters from '../../mixins/filters.'
    import * as FilterTypes from '../../constants/filter-types'
    import * as ColumnManagerTypes from '../../constants/column-manager-types'

    export default {
        name: "OrderList",

        mixins: [pagination, filters],

        data() {
            let columns = [
                {label: 'Order Date', prop: 'ordered_at', showOverflowTooltip: true, sortable: true, isFixed: true, width: '100'},
                {label: 'Order Number', prop: 'order_no', showOverflowTooltip: true, sortable: true, isFixed: true, width: '100'},
                {label: 'Serial No.', prop: 'serial_id', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Product Code', prop: 'blind_type', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Product Type', prop: 'product_type', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Acc. Code', prop: 'account_code', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Customer', prop: 'customer', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Cust. Ref.', prop: 'customer_ref', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Qty', prop: 'qty', showOverflowTooltip: true, sortable: true, width: '80'},
                {label: 'Width', prop: 'width', showOverflowTooltip: true, sortable: true, width: '80'},
                {label: 'Drop', prop: 'drop', showOverflowTooltip: true, sortable: true, width: '80'},
                {label: 'Stock Code', prop: 'stock_code', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Range', prop: 'range', showOverflowTooltip: true, sortable: true, width: '80'},
                {label: 'Color', prop: 'color', showOverflowTooltip: true, sortable: true, width: '80'},
                {label: 'Item Price', prop: 'item_price', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Work Date', prop: 'work_date', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Status', prop: 'order_status', showOverflowTooltip: true, sortable: true, width: '80'},
                {label: 'Last Updated', prop: 'last_updated_at', showOverflowTooltip: true, sortable: true, width: '100'},
                {label: 'Updated By', prop: 'updated_by', showOverflowTooltip: true, sortable: true, width: '100'},
            ]

            return {
                loading: false,
                showColumnManager: false,
                searchString: null,
                pagination: {},
                orders: [],
                columns: columns,
                selectedColumns: [],
                FilterTypes,
                ColumnManagerTypes
            }
        },

        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'

            this.initializeList()
        },

        methods: {
            applyFilters() {
                if (this.filters.id) {
                    this.updateFilter()
                } else {
                    this.createFilter()
                }

                this.getList()
            },

            getList() {
                this.loading = true

                let params = {...this.filters.filters, ...this.pagination}
                params.searchString = this.searchString

                this.$API.Orders.orderList(params)
                .then(res => {
                    if (res.data.data) {
                        this.orders = res.data.data
                        this.pagination.total = res.data.total
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            applySearch: debounce(function () {
                this.getList()

                this.pagination.page = 1
            }, 500),

            exportOrders() {
                this.loading = true

                let postData = {
                    filters: this.filters.filters,
                    headers: this.getExportableHeaders()
                }

                this.$API.Orders.exportOrders(postData)
                .then(res => {
                    if (res.data.success) {
                        this.$notify({
                            title: 'Team Status Report',
                            message: res.data.message,
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

            getExportableHeaders() {
                return _.assign.apply(_, this.columns.filter(col => this.selectedColumns.includes(col.prop))
                    .map(col => {
                        let newObj = {}
                        newObj[col.prop] = col.label

                        return newObj
                    }))

            },

            async initializeList() {
                await this.getFilterByType(FilterTypes.TYPE_ORDER)

                await this.getList()
            },

            initializeFilter() {
                let now = moment()
                let defaultDates = [now.clone().subtract(30, 'days').format('YYYY-MM-DD'), now.format('YYYY-MM-DD')]

                this.filters.type = FilterTypes.TYPE_ORDER
                this.filters.filters = {
                    dates: defaultDates,
                    employees: [],
                    processes: []
                }
            },

            manageTableColumns() {
                this.showColumnManager = true
            },

            closeColumnManager() {
                this.showColumnManager = false
            },

            updateSelectedColumns(columns) {
                this.selectedColumns = cloneDeep(columns)

                this.$nextTick(_ => {
                    if (this.$refs.ordersTable) {
                        this.$refs.ordersTable.doLayout()
                    }
                })
            },

            getCellStyle({row, column, rowIndex, columnIndex}) {
                if (column.property === 'order_status' && row.scanner) {
                    let color = row.scanner.color || ''
                    return `
                        background-color: ${color};
                        color: ${color ? '#fff' : '#000'}
                    `
                }
            }
        }
    }
</script>
