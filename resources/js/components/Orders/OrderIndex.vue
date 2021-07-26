<template>
    <el-card
        class="box-card"
        v-loading="loading">
        <order-search-form :type="pageData.type"></order-search-form>
    </el-card>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from '../../mixins/pagination'
    export default {
        name: "OrderIndex",
        mixins: [pagination],
        props: {
            pageData: {}
        },
        data() {
            return {
                loading: false,
                orders: [],
                filters: {
                    searchString: null
                }
            }
        },
        created() {
            this.functionName = 'fetch'
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
