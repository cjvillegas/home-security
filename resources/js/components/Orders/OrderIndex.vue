<template>
    <el-card class="box-card">
        <el-table
            fit
            :data="orders">
            <el-table-column
                v-for="column in columns"
                :key="column.key"
                :prop="column.key"
                :label="column.label"
                :show-overflow-tooltip="column.show_overflow_tooltip"
                sortable>
                <template slot-scope="scope">
                    <span>Googke is the key</span>
                </template>
            </el-table-column>

            <el-table-column label='Actions'>

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
        methods: {

        }
    }
</script>

<style scoped>

</style>
