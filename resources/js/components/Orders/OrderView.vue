<template>
    <div>
        <el-button
            @click="backToOrderList">
            <i class="fas fa-arrow-circle-left"></i> Back
        </el-button>

        <el-card
            class="box-card mt-3"
            v-loading="loading">
            <h4>Orders</h4>
            <el-input
                placeholder="Press enter to search order details..."
                clearable
                style="width: 250px"
                v-model="search"
                @keyup.enter.native="applySearch">
            </el-input>

            <el-table
                fit
                :data="filteredOrder"
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

        <order-scanners
            :scanners-list="scanners">
        </order-scanners>

        <order-progress
            :orders="orders"
            :processes="processes">
        </order-progress>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from '../../mixins/pagination'
    export default {
        name: "OrderView",
        mixins: [pagination],
        props: {
            toSearch: {
                required: true
            },
            field: {
                required: false,
                type: String,
                default: 'order_no'
            }
        },
        data() {
            let columns = [
                {label: 'ID', key: 'id', show_overflow_tooltip: true},
                {label: 'Blind ID', key: 'blind_id', show_overflow_tooltip: true},
                {label: 'Order No.', key: 'order_no', show_overflow_tooltip: true},
                {label: 'Customer', key: 'customer', show_overflow_tooltip: true},
                {label: 'Quantity', key: 'quantity', show_overflow_tooltip: true},
                {label: 'Blind Type', key: 'blind_type', show_overflow_tooltip: true},
                {label: 'Blind Status', key: 'blind_status', show_overflow_tooltip: true},
                {label: 'Ordered By', key: 'ordered_by_name', show_overflow_tooltip: true},
                {label: 'Serial ID', key: 'serial_id', show_overflow_tooltip: true},
                {label: 'Ordered Date', key: 'ordered_date', show_overflow_tooltip: true},
            ]

            return {
                loading: false,
                orders: [],
                scanners: [],
                columns: columns,
                filters: {
                    searchString: null
                },
                search: null,
                processes: []
            }
        },
        created() {
            this.getProcesses()
            this.loadData()
        },
        methods: {
            applySearch() {
                this.filters.page = 1
                this.filters.searchString = cloneDeep(this.search)
            },
            loadData() {
                if (this.field === 'order_no') {
                    this.getOrderDetails()

                    return false
                }

                this.getScannersData()
            },
            getOrderDetails() {
                this.loading = true

                this.$API.Orders.getOrderDetails(this.field, this.toSearch)
                    .then(res => {
                        this.orders = cloneDeep(res.data || [])
                        this.filters.total = this.orders.length
                        this.scanners = this.orders.reduce((acc, cur) => acc = [...acc, ...cur.scanners], [])
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            getScannersData() {
                this.loading = true

                this.$API.Scanners.getScannersByField(this.field, this.toSearch)
                    .then(res => {
                        this.scanners = cloneDeep(res.data || [])
                        this.filters.total = this.orders.length
                        this.orders = this.scanners.reduce((acc, cur) => acc = [...acc, ...(cur.order && !acc.some(o => o.id === cur.order.id)) ? [cur.order] : []], [])
                            .map(or => {
                                or.scanners = this.scanners.filter(sc => sc.blindid === or.serial_id)
                                return or
                            })
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            getProcesses() {
                this.$API.Processes.getAll()
                    .then(res => {
                        this.processes = cloneDeep(res.data)
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            backToOrderList() {
                this.$router.push({name: 'Order List'})
            }
        },
        computed: {
            filteredOrder() {
                let orders = cloneDeep(this.orders)
                let page = this.filters.page
                let offset = (page - 1) * this.filters.size
                let size = this.filters.size * page

                // checks if the search query is present
                if (this.search) {
                    let query = this.search.toLowerCase()

                    // do the local searching
                    orders = orders.filter(order => {
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

                this.filters.total = orders.length

                // do local pagination
                // this retrieve orders in between the current offset and the limit
                orders = orders.filter((item, index) => (index + 1) > offset && (index + 1) <= size)

                return orders
            }
        },
        beforeRouteEnter(to, from, next) {
            // if the route is loaded from the URL section and not from the
            // search page, redirect it directly to the search page
            if (to.params && !to.params.field) {
                // next({replace: true, name: "Order List"})
            }

            // proceed if else
            next()
        }
}
</script>
