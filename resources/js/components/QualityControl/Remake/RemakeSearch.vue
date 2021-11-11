<template>
    <div>
        <el-input
            placeholder="Scan or Type Order Number"
            v-model="filters.order_no"
            @keyup.enter.native.prevent="getOrdersAction"
            clearable="">
        </el-input>
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
            ...mapGetters('remakechecker', ['orders'])
        },

        methods: {
            ...mapActions('remakechecker', ['getOrders']),

            getOrdersAction() {
                this.getOrders(this.filters)
            }
        }
    }
</script>
