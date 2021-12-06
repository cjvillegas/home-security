<template>
    <el-card class="box-card">
        <div v-loading="loading">
        <div class="d-flex">
            <div>
                <el-input
                    v-model="filters.searchString"
                    clearable
                    placeholder="Search process categories..."
                    @keyup.enter.native.prevent="getList"
                    style="width: 250px">
                </el-input>
            </div>

            <div class="ml-auto">
                <el-button
                    v-if="user.permissions.process_categories_create"
                    type="primary"
                    @click="showForm = !showForm">
                    <i class="fas fa-plus"></i> Add Process Category
                </el-button>
            </div>
        </div>

        <div>
            <el-table
                fit
                :data="processCategories">
                <el-table-column
                    label="No."
                    type="index"
                    sortable>
                </el-table-column>

                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop">
                    <template slot-scope="scope">
                        {{ $StringService.ucwords(scope.row[column.prop]) }}
                    </template>
                </el-table-column>

                <el-table-column
                    v-if="user.permissions.process_categories_show || user.permissions.process_categories_edit || user.permissions.process_categories_delete"
                    label="Actions">
                    <template slot-scope="scope">
                        <el-button
                            v-if="user.permissions.process_categories_show"
                            @click="viewProcessCategory(scope.row)"
                            type="text"
                            class="text-info">
                            <i class="fas fa-eye"></i>
                        </el-button>

                        <el-button
                            v-if="user.permissions.process_categories_edit"
                            @click="stageProcessCategory(scope.row)"
                            type="text"
                            class="ml-2">
                            <i class="fas fa-pencil-alt"></i>
                        </el-button>

                        <el-popconfirm
                            v-if="user.permissions.process_categories_delete"
                            @confirm="deleteProcessCategory(scope.row)"
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
                </el-table-column>
            </el-table>

            <div class="text-right">
                <el-pagination
                    class="mt-3"
                    background
                    layout="total, sizes, prev, pager, next"
                    :total="pagination.total"
                    :page-size="pagination.size"
                    :page-sizes="[10, 25, 50, 100]"
                    :current-page="pagination.page"
                    @size-change="handleSize"
                    @current-change="handlePage">
                </el-pagination>
            </div>

        </div>

        <settings-process-categories-form
            :model="model"
            :mode="mode"
            :visible.sync="showForm"
            @close="closeForm">
        </settings-process-categories-form>
    </div>
    </el-card>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from '../../mixins/pagination'

    export default {
        name: "SettingsProcessCategories",
        mixins: [pagination],
        props: {
            user: {
                required: true,
                type: Object
            }
        },
        data() {
            let columns = [
                {prop: 'code', label: 'Code', showOverflowTooltip: true, sortable: true},
                {prop: 'name', label: 'Name', showOverflowTooltip: true, sortable: true},
            ]
            return {
                filters: {
                    searchString: null
                },
                columns: columns,
                showForm: false,
                loading: false,
                mode: 'create',
                model: null,
                processCategories: []
            }
        },
        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'

            this.getList()

            this.$EventBus.listen('SETTINGS_PROCESS_CATEGORIES_CREATE', _ => {
                this.getList()
            })

            this.$EventBus.listen('SETTINGS_PROCESS_CATEGORIES_UPDATE', _ => {
                this.getList()
            })
        },
        methods: {
            getList() {
                this.loading = true

                let params = {...this.filters, ...this.pagination}

                this.$API.ProcessCategory.getList(params)
                .then(res => {
                    this.processCategories = res.data.data
                    this.pagination.total = res.data.total
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },
            closeForm() {
                this.showForm = false
                this.model = null
                this.mode = 'create'
            },
            viewProcessCategory(processCategory) {
                this.showForm = true
                this.mode = 'view'
                this.model = cloneDeep(processCategory)
            },
            stageProcessCategory(processCategory) {
                this.showForm = true
                this.mode = 'update'
                this.model = cloneDeep(processCategory)
            },
            deleteProcessCategory(processCategory) {
                this.loading = true

                this.$API.ProcessCategory.delete(processCategory.id)
                .then(res => {
                    this.getList()
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            }
        }
    }
</script>

<style scoped>

</style>
