<template>
    <div>
        <el-form :model="searchForm">
            <el-form-item
                label="Search Order By"
                prop="field">
                <el-select
                    v-model="searchForm.field"
                    class="w-100">
                    <el-option label="Order No." value="order_no"></el-option>
                    <el-option label="Blind No." value="blind_id"></el-option>
                </el-select>
            </el-form-item>

            <el-form-item
                :label="toSearchLabel"
                prop="searchString">
                <el-autocomplete
                    v-model="searchForm.searchString"
                    clearable
                    class="w-100"
                    label="customer"
                    :fetch-suggestions="querySearch"
                    @select="selectOrder"
                    placeholder="Enter to search orders...">
                    <template slot-scope="{item}">
                        <div class="d-flex align-items-center">
                            <span class="font-base font-bold">{{ item[searchForm.field] }}</span>
                            <span class="ml-2 mr-2">|</span>
                            <span class="text-gray-400">{{ item.customer_order_no }}</span><br>
                        </div>
                    </template>
                </el-autocomplete>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
export default {
    name: "OrderSearchForm",
    data() {
        return {
            searchForm: {
                field: 'order_no',
                searchString: null
            }
        }
    },
    methods: {
        querySearch(searchString, cb) {
            this.$API.Orders.searchOrderByField(this.searchForm.field, searchString)
            .then(res => {
                cb(res.data)
            })
            .catch(err => {
                console.log(err)
            })
            .finally(_ => {})
        },
        selectOrder(order) {
            this.$router.push({name: 'Order View', params: {orderNo: order.order_no}})
        },
    },
    computed: {
        toSearchLabel() {
            let label = this.searchForm.field.split('_')

            return `Search ${this.$StringService.ucwords(label[0])} No.`
        }
    }
}
</script>
