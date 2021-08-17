<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Permission List</h4>
        </el-card>
        <div v-loading="loading">
            <el-card class="box-card mt-3">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Permission..."
                            style="width: 250px"
                            @keyup.enter.native.prevent="fetchPermissions">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="addNew">
                            <i class="fas fa-plus"></i> Add Permission
                        </el-button>
                    </div>
                </div>

                <el-table
                    :data="permissions"
                    class="w-100"
                    fit>
                    <el-table-column
                        prop="qc_code"
                        label="Quality Control code"
                        sortable>
                    </el-table-column>

                </el-table>

                <div class="text-right">
                    <el-pagination
                        class="mt-3"
                        background
                        layout="total, sizes, prev, pager, next"
                        :total="filters.total"
                        :page-size="filters.size"
                        :page-sizes="[10, 25, 50, 100]"
                        :current-page="filters.page"
                        @size-change="handleSize"
                        @current-change="handlePage">
                    </el-pagination>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script>
    import pagination from '../../mixins/pagination'
    import { formHelper } from '../../mixins/formHelper'

    export default {
        mixins: [pagination, formHelper],
        data() {
            return {
                loading: false,
                filters: {
                    searchString: null,
                },
                permissions: []
            }
        },

        created() {
            this.filters.size = 10
            this.functionName = 'fetchPermissions'
        },

        mounted() {
            this.fetchPermissions()
        },

        methods: {
            fetchPermissions() {
                let apiUrl = `/admin/permissions/list`
                this.loading = true

                axios.post(apiUrl, this.filters)
                .then((response) => {
                    console.log(response.data)
                    this.permissions = response.data.permissions.data
                    this.filters.total = response.data.permissions.total
                })
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {
                    this.loading = false
                })
            },

            addNew() {

            }
        }
    }
</script>

<style>

</style>
