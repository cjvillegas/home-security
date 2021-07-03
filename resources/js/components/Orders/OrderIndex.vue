<template>
    <el-card
        class="box-card"
        v-loading="loading">
        <el-input
            placeholder="Press enter to search orders..."
            clearable
            style="width: 250px"
            v-model="filters.searchString"
            @keyup.enter.native="applySearch">

        </el-input>

        <el-table
            fit
            :data="orders"
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

            <el-table-column label='Actions'>
                <template slot-scope="scope">
                    <el-button
                        @click="viewOrder(scope.row)"
                        size="mini">
                        <i class="fas fa-eye"></i> View
                    </el-button>
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
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from '../../mixins/pagination'
    export default {
        name: "OrderIndex",
        mixins: [pagination],
        data() {
            let columns = [
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
                columns: columns,
                filters: {
                    searchString: null
                }
            }
        },
        created() {
            this.functionName = 'fetch'

            this.fetch()
        },
        methods: {
            applySearch() {
                this.filters.page = 1

                this.fetch()
            },
            fetch() {
                this.loading = true

                this.$API.Orders.fetch(
                    this.filters.size,
                    this.filters.page,
                    this.filters.searchString
                )
                .then(res => {
                    this.orders = cloneDeep(res.data.data || [])
                    this.filters.total = res.data.total
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },
            viewOrder(order) {
                this.$router.push({name: 'Order View', params: {orderNo: order.order_no}})
            }
        }
    }
</script>
