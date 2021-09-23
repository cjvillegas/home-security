<template>
    <div>
        <el-button
            @click="backToOrderList">
            <i class="fas fa-arrow-circle-left"></i> Back
        </el-button>

        <el-card
            class="box-card mt-3"
            v-loading="loading">
            <h4>Order Info</h4>
            <order-info
                :order="order"
                :scanners-list="scanners">
            </order-info>
        </el-card>

        <order-scanners
            ref="scanners"
            :user="user"
            :scanners-list="scanners">
        </order-scanners>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from '../../mixins/pagination'
import { mapActions } from 'vuex'
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
            return {
                loading: false,
                order: null,
                scanners: [],
                filters: {
                    searchString: null
                },
                user: null,
                orderTrackings: [],
            }
        },

        created() {
            this.loadData()
            this.getAuthUser()
        },

        methods: {
            ...mapActions('orders', ['setOrderNo']),
            getAuthUser() {
                this.$API.User.getAuthUser()
                    .then(res => {
                        this.user = res.data
                    })
            },
            loadData() {
                this.getOrderDetails()
            },

            getOrderScannersData() {
                this.$refs.scanners ? this.$refs.scanners.loading = true : null

                this.$API.Orders.getOrderScannersData(this.order.order_no)
                .then(res => {
                    this.scanners = res.data
                    this.order.scanners = cloneDeep(res.data)
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.$refs.scanners ? this.$refs.scanners.loading = false : null
                })
            },
            getOrderDetails() {
                this.loading = true
                this.$API.Orders.getOrderDetails(this.field, this.toSearch)
                    .then(res => {
                        this.order = cloneDeep(res.data)

                        this.getOrderScannersData()
                        this.setOrderNo(this.order.order_no)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            backToOrderList() {
                this.$router.push({name: 'Order List'})
            },
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
