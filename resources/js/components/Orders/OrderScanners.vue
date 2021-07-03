<template>
    <div>
        <el-card
            class="box-card mt-3"
            v-loading="loading">
            <h4>Scanners</h4>
            <el-input
                placeholder="Press enter to search order scanners..."
                clearable
                style="width: 250px"
                v-model="search"
                @keyup.enter.native="applySearch">
            </el-input>

            <el-table
                fit
                :data="filteredScanners"
                class="mt-3">
                <el-table-column
                    v-for="column in columns"
                    :key="column.key"
                    :prop="column.key"
                    :label="column.label"
                    :show-overflow-tooltip="column.show_overflow_tooltip"
                    sortable>
                    <template slot-scope="scope">
                        <span>{{ scope.row[column.key] }}</span>
                    </template>
                </el-table-column>
            </el-table>

            <div class="text-right mt-3">
                <el-pagination
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
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from "../../mixins/pagination";
    export default {
        name: "OrderScanners",
        mixins: [
            pagination
        ],
        props: {
            scannersList: {
                type: Array,
                required: true
            }
        },
        data() {
            let columns = [
                {label: 'Employee', key: 'employee_name', show_overflow_tooltip: true},
                {label: 'Operation', key: 'operation', show_overflow_tooltip: true},
                {label: 'Blind ID', key: 'blind_id', show_overflow_tooltip: true},
                {label: 'Scanned At', key: 'scanned_at', show_overflow_tooltip: true},
                {label: 'Shift', key: 'shift', show_overflow_tooltip: true},
                {label: 'Team', key: 'team', show_overflow_tooltip: true},
            ]

            return {
                loading: false,
                scanners: [],
                columns: columns,
                filters: {
                    searchString: null
                },
                search: null
            }
        },
        methods: {
            formatScanners() {
                let scanners = cloneDeep(this.scanners)

                this.scanners = scanners.map(scanner => {
                    scanner.employee_name = this.$StringService.ucwords(scanner.employee ? scanner.employee.fullname : '')
                    scanner.operation = this.$StringService.ucwords(scanner.process ? scanner.process.name : '')
                    scanner.blind_id = scanner.blindid
                    scanner.scanned_at = this.$DateService.formatDateTime(scanner.scannedtime, 'MM/DD/YYYY HH:mm:ss', 'MMM DD, YYYY HH:mm')
                    scanner.shift = scanner.employee && scanner.employee.shift ? scanner.employee.shift.name : ''
                    scanner.team = scanner.employee && scanner.employee.team ? scanner.employee.team.name : ''

                    return scanner
                })
            }
        },
        computed: {
            filteredScanners() {
                let scanners = cloneDeep(this.scanners)

                let page = this.filters.page
                let offset = (page - 1) * this.filters.size
                let size = this.filters.size * page

                // checks if the search query is present
                if (this.search) {
                    let query = this.search.toLowerCase()

                    // do the local searching
                    scanners = scanners.filter(order => {
                        let blindId = order.blind_id ? order.blind_id.toString().toLowerCase() : ''
                        let customer = order.customer ? order.customer.toString().toLowerCase() : ''
                        let quantity = order.quantity ? order.quantity.toString().toLowerCase() : ''
                        let blindType = order.blind_type ? order.blind_type.toString().toLowerCase() : ''
                        let blindStatus = order.blind_status ? order.blind_status.toString().toLowerCase() : ''
                        let serialId = order.serial_id ? order.serial_id.toString().toLowerCase() : ''

                        return blindId.indexOf(query) > -1 || customer.indexOf(query) > -1 || quantity.indexOf(query) > -1 || blindType.indexOf(query) > -1
                            || blindStatus.indexOf(query) > -1 || serialId.indexOf(query) > -1
                    })
                }

                // do local pagination
                // this retrieve scanners in between the current offset and the limit
                scanners = scanners.filter((item, index) => (index + 1) > offset && (index + 1) <= size)

                return scanners
            }
        },
        watch: {
            scannersList: {
                handler() {
                    this.scanners = cloneDeep(this.scannersList)

                    this.formatScanners()
                },
                deep: true,
                immediate: true
            }
        }
    }
</script>

<style scoped>

</style>
