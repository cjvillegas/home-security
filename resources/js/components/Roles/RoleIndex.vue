<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Roles List</h4>
        </el-card>
        <div v-loading="loading">
            <el-card class="box-card mt-3">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Roles..."
                            style="width: 250px"
                            @keyup.enter.native.prevent="fetchRoles">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="addNew">
                            <i class="fas fa-plus"></i> Add Role
                        </el-button>
                    </div>
                </div>
                <el-table
                    :data="roles"
                    class="w-100"
                    fit>

                    <el-table-column
                        prop="id"
                        label="ID"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        prop="title"
                        label="Title"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        width="100%"
                        label="Action"
                        class-name="table-action-button">
                        <template slot-scope="scope">
                            <template>
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="Edit"
                                    placement="top"
                                    :open-delay="1000">
                                    <el-button
                                        @click="openEditDialog(scope.row), formDialogVisible = true"
                                        type="text">
                                        <i class="fas fa-pen"></i>
                                    </el-button>
                                </el-tooltip>
                                <el-popconfirm
                                    @confirm="deleteTeam(scope.row.id)"
                                    confirm-button-text='OK'
                                    cancel-button-text='No, Thanks'
                                    icon="el-icon-info"
                                    icon-color="red"
                                    title="Are you sure to delete this Role?">
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
                formDialogVisible: false,
                roles: [],
                filters: {
                    searchString: null,
                }
            }
        },

        created() {
            this.filters.size = 10
            this.functionName = 'fetchRoles'
        },

        mounted() {
            this.fetchRoles()
        },

        methods: {
            fetchRoles() {
                let apiUrl = `/admin/roles/list`
                this.loading = true

                axios.post(apiUrl, this.filters)
                .then((response) => {
                    this.roles = response.data.roles.data
                    this.filters.total = response.data.roles.total
                })
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {
                    this.loading = false
                })
            },

            addNew() {

            },

            openEditDialog() {

            },

            getDefaultValues() {
                return {
                    id: null,
                    title: null
                }
            }
        }
    }
</script>
