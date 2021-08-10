<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        @close="closeForm"
        top="5vh"
        width="40%">
        <el-form
            v-loading="loading">
            <el-form-item
                label="Stock Item...">
                <el-autocomplete
                    v-model="stock_item"
                    :fetch-suggestions="querySearch"
                    placeholder="Stock Item Name"
                    value-key="stock_code">
                </el-autocomplete>
            </el-form-item>
        </el-form>
    </el-dialog>
</template>

<script>
import {dialog} from "../../mixins/dialog";
export default {
    props: ['id'],
    mixins: [dialog],
    data() {
        return {
            loading: false,
            stock_item: '',
            dialogTitle: "View Stock Level"
        }
    },

    methods: {
        closeForm() {
            this.closeModal()
        },

        querySearch(queryString, cb) {
            let apiUrl = `/admin/in-house/stocks/list`
            var stockItems = []
            this.loading = true

            axios.post(apiUrl, this.filters)
            .then((response) => {
                stockItems = response.data.stockItems.data
                var results = queryString ? stockItems.filter(this.createFilter(queryString)) : stockItems

                cb(results)
            })
            .catch( (err) => {
                console.log(err)
            })
            .finally( () => {
                this.loading = false
            })
        },

        createFilter(queryString) {
            return (stockItems) => {
                return (stockItems.stock_code.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
            };
        },
    }
}
</script>
