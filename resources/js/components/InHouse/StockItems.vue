<template>
    <div>
        <el-card class="box-card">
            <div class="d-flex">
                <div class="ml-auto">
                    <el-button v-if="!isForm"
                        type="primary"
                        @click="toggleForm('add')">
                        <i class="fas fa-plus"></i> Add Stock Item
                    </el-button>
                    <el-button v-else
                        type="primary"
                        @click="toggleForm('back')">
                        <i class="fa fa-arrow-left"></i> Return
                    </el-button>
                </div>
            </div>
            <div v-loading="loading">
                <el-table
                    :data="stockItems"
                    v-show="!isForm">
                    <el-table-column
                        prop="stock_code"
                        label="Stock Code">

                    </el-table-column>
                </el-table>
            </div>
            <stock-item-form
                :formTitle="formTitle"
                :data="form"
                v-show="isForm"
                @clicked="saveStockItem">
            </stock-item-form>
        </el-card>


    </div>

</template>

<script>
import StockItemForm from './StockItemForm.vue';
export default {
  components: { StockItemForm },
    props: {
        user: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            loading: false,
            filters: {},
            form: {
            },
            formTitle: '',
            stockItems: [],
            isForm: false
        }
    },

    mounted() {
        this.filters.size = 10
        this.fetchStocks();
    },

    methods: {
        fetchStocks() {
            let apiUrl = `/admin/in-house/stocks/list`

            axios.post(apiUrl, this.filters)
            .then((response) => {
                console.log(response.data.stockItems)
                this.stockItems = response.data.stockItems.data
            })
        },

        saveStockItem(data) {
            console.log(data)
            this.isForm = false
        },

        toggleForm(action) {
            if (action == 'add') {
                this.formTitle = 'Add Stock Items'
                this.isForm = true
                return
            }
            else if (action == 'back') {
                this.isForm = false
            }
        }
    }

}
</script>

<style>

</style>
