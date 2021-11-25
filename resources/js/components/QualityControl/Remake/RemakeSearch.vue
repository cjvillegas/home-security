<template>
    <div v-loading="loading">
        <div class="d-flex justify-content-center">
            <i class="fa fa-pencil-square-o fa-5x" aria-hidden="true"></i>
        </div>
        <div class="d-flex justify-content-center">
            <h1>Welcome, {{ $root.user.name }}!</h1>
        </div>
        <span class="d-flex justify-content-center mt-2"> In this section you will be validating remake for a specific order. </span>
        <span class="d-flex justify-content-center"> To begin, please scan or type the Order Number.</span>
        <div class="d-flex justify-content-center mt-2">
            <el-form
                :inline="true"
                size="mini"
                class="mt-2">
                <el-form-item>
                    <el-input
                        prefix-icon="el-icon-search"
                        placeholder="Scan or Type Order Number"
                        v-model="filters.order_no"
                        @keyup.enter.native.prevent="getOrdersAction"
                        clearable="">
                    </el-input>
                </el-form-item>
                <el-form-item>
                    <el-button
                        type="primary"
                        icon="el-icon-search"
                        @click="getOrdersAction">
                        Search
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>
<script>
    import { mapActions, mapGetters } from 'vuex'
    export default {
        data() {
            return {
                filters: {
                    order_no: null
                }
            }
        },

        computed: {
            ...mapGetters('remakeChecker', ['orders', 'loading'])
        },

        methods: {
            ...mapActions('remakeChecker', ['getOrders']),

            getOrdersAction() {
                this.getOrders(this.filters)
                .then(() => {
                    if (this.orders.length == 0) {
                        this.$notify({
                            title: 'Remake Checker',
                            message: 'No Records Found',
                            type: 'error'
                        })
                        return
                    }
                })
            }
        }
    }
</script>
