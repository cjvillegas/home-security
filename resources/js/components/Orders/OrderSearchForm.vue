<template>
    <div>
        <el-form :model="searchForm">
            <el-form-item
                label="Search Order By"
                prop="field">
                <el-select
                    disabled
                    v-model="searchForm.field"
                    class="w-100">
                    <el-option label="Order No." value="order_no"></el-option>
                    <el-option label="Blind No." value="serial_id"></el-option>
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
                            <span class="text-gray-400">{{ item.customer_order_no || item.processid }}</span><br>
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
    props: {
        type: {
            type: String
        }
    },
    data() {
        return {
            searchForm: {
                field: this.type,
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
            this.$router.push({name: 'Order View', params: {toSearch: order[this.searchForm.field], field: this.searchForm.field}})
        },
    },
    computed: {
        toSearchLabel() {
            return `Search ${this.searchForm.field === 'order_no' ? 'Order No.' : 'Blind ID'}`
        }
    }
}
</script>
