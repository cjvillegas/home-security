<template>
    <el-card>
        <div class="d-flex">
            <div>
                <el-input
                    v-model="filters.searchString"
                    clearable
                    placeholder="Search Stock Level name or code..."
                    @keyup.enter.native.prevent="fetchStockLevels"
                    style="width: 250px">
                </el-input>
            </div>
        </div>
        <div v-loading="loading">
            <el-table
                :data="stockLevels">
                <el-table-column
                    prop="code"
                    label="Code"
                    sortable>
                </el-table-column>

                <el-table-column
                    prop="name"
                    label="Name"
                    sortable>
                </el-table-column>

                <el-table-column
                    prop="availablestock"
                    label="Available Stock"
                    sortable>
                </el-table-column>

                <el-table-column
                    prop="postock"
                    label="Post Stock"
                    sortable>
                </el-table-column>

                <!-- <el-table-column
                    label="View"
                    class-name="table-action-button">
                    <template slot-scope="scope">
                        <template>
                            <el-button
                                @click="viewStockLevel(scope.row)"
                                type="text"
                                class="text-info">
                                <i class="fas fa-eye"></i>
                            </el-button>
                        </template>
                    </template>
                </el-table-column> -->
            </el-table>
        </div>
        <el-pagination
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

        <!-- <stock-level-view
            :visible.sync="viewDialogVisible"
            :id="selected_id"
            @close="closeForm">
        </stock-level-view> -->
    </el-card>
</template>

<script>
import pagination from '../../mixins/pagination'
export default {
    mixins: [pagination],
    props: {
        user: {
            required: true,
            type: Object
        }
    },

    data() {
        return {
            loading: false,
            stockLevels: [],
            filters: {
                searchString: ''
            },
            selected_id: '',
            viewDialogVisible: false
        }
    },

    mounted() {
        this.filters.size = 10
        this.functionName = 'fetchStockLevels'
        this.fetchStockLevels();
    },

    methods: {
        fetchStockLevels() {
            let apiUrl = `/admin/in-house/stocklevels/list`
            this.loading = true
            axios.post(apiUrl, this.filters)
            .then((response) => {
                this.stockLevels = response.data.stockLevels.data
                this.filters.total = response.data.stockLevels.total
            })
            .catch( () => {

            })
            .finally( () => {
                this.loading = false
            })
        },

        viewStockLevel(id) {
            this.selected_id = id
            this.viewDialogVisible = true
        },

        closeForm() {
            this.viewDialogVisible = false
        }
    }
}
</script>

<style>

</style>
