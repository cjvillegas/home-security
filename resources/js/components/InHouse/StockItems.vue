<template>
    <div>
        <el-card v-if="isForm">
            <el-button
                type="text"
                @click="toggleForm('back')">
                <i class="fa fa-arrow-left"></i> Return
            </el-button>
        </el-card>
        <el-card class="box-card">
            <div class="d-flex">
                <div v-show="!isForm">
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search Stock Item..."
                        @keyup.enter.native.prevent="fetchStocks"
                        style="width: 250px">
                    </el-input>
                </div>

                <div class="ml-auto">
                    <el-button v-if="!isForm"
                        type="primary"
                        @click="toggleForm('add')">
                        <i class="fas fa-plus"></i> Add Stock Item
                    </el-button>
                </div>
            </div>
            <div v-loading="loading">
                <el-table
                    :data="stockItems"
                    v-show="!isForm">
                    <el-table-column
                        prop="stock_code"
                        label="Stock Code"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="range"
                        label="Range"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="colour"
                        label="Colour"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="size"
                        label="Size"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="length"
                        label="Length"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="status"
                        label="Status"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        label="Action"
                        class-name="table-action-button">
                        <template slot-scope="scope">
                            <template>
                                <el-button
                                    @click="viewStockItem(scope.row)"
                                    type="text"
                                    class="text-info">
                                    <i class="fas fa-eye"></i>
                                </el-button>
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="Edit"
                                    placement="top"
                                    :open-delay="1000">
                                    <el-button
                                        type="text"
                                        @click="editStockItem(scope.row)">
                                        <i class="fas fa-pen"></i>
                                    </el-button>
                                </el-tooltip>
                                <el-popconfirm
                                    @confirm="deleteStockItem(scope.row.id)"
                                    confirm-button-text='OK'
                                    cancel-button-text='No, Thanks'
                                    icon="el-icon-info"
                                    icon-color="red"
                                    title="Are you sure to delete this?">
                                    <el-button
                                        type="text"
                                        class="text-danger ml-2"
                                        slot="reference">
                                        <i class="fas fa-trash-alt"></i>
                                    </el-button>
                                </el-popconfirm>
                            </template>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <el-pagination
                v-show="!isForm"
                class="custom-pagination-class  mt-3 float-right"
                background
                layout="total, sizes, prev, pager, next"
                :total="filters.total"
                :page-size="filters.size"
                :page-sizes="[1, 2, 10, 25, 50, 100]"
                :current-page="filters.page"
                @size-change="handleSize"
                @current-change="handlePage">
            </el-pagination>
            <stock-item-form
                :formTitle="formTitle"
                :model="model"
                :type="type"
                v-show="isForm"
                ref="stockItemForm"
                @saved="saveStockItem"
                @toggle="toggleForm">
            </stock-item-form>
        </el-card>


    </div>

</template>

<script>
import pagination from '../../mixins/pagination'
import { formHelper } from '../../mixins/formHelper'
import cloneDeep from 'lodash/cloneDeep'
import StockItemForm from './StockItemForm.vue';
export default {
  components: {StockItemForm },
  mixins: [pagination, formHelper],
    props: {
        user: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            loading: false,
            filters: {
                searchString: ''
            },
            form: {
            },
            model: null,
            formTitle: '',
            type: '',
            stockItems: [],
            isForm: false
        }
    },

    mounted() {
        this.filters.size = 10
        this.functionName = 'fetchStocks'
        this.fetchStocks();
    },

    methods: {
        fetchStocks() {
            let apiUrl = `/admin/in-house/stocks/list`
            this.loading = true
            axios.post(apiUrl, this.filters)
            .then((response) => {
                this.stockItems = response.data.stockItems.data
                this.filters.total = response.data.stockItems.total
            })
            .catch( () => {})
            .finally( () => {
                this.loading = false
            })
        },

        saveStockItem(data) {
            let apiUrl = `/admin/in-house/stocks`
            this.loading = true

            axios.post(apiUrl, data)
            .then( (response) => {
                this.$notify({
                    title: 'Success!',
                    message: response.data.message,
                    type: 'success'
                });
                this.isForm = false
                this.fetchStocks()
            })
            .catch((err) => {

            })
            .finally( () => {
                this.loading = false
            })

        },

        updateStockItem(data, id) {

        },

        viewStockItem(data) {
            this.isForm = true
            this.type = 'view'
            this.formTitle = 'View Stock Item'
            this.model = cloneDeep(data)
        },

        editStockItem(data) {
            this.isForm = true
            this.type = 'edit'
            this.formTitle = 'Edit Stock Item'
            this.model = cloneDeep(data)
        },

        deleteStockItem(id) {
            let apiUrl = `/admin/in-house/stocks/${id}/destroy`
            this.loading = true
            axios.delete(apiUrl).then((response) => {
                this.fetchStocks()
                this.$notify({
                    title: 'Success!',
                    message: response.data.message,
                    type: 'success'
                });
            })
            .catch( (err) => {

            })
            .finally( () => {
                this.loading.false
            })
        },

        toggleForm(action) {
            if (action == 'add') {
                this.type='add'
                this.model = null
                this.formTitle = 'Add Stock Items'
                this.isForm = true
                return
            }
            else if (action == 'back') {
                this.$refs.stockItemForm.clearForm()
                this.isForm = false
                this.model = null
                this.fetchStocks()
            }
        }
    }

}
</script>

<style>

</style>
