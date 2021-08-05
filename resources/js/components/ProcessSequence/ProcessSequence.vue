<template>
    <el-card class="box-card">
        <div v-loading="loading">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search process sequences..."
                        @keyup.enter.native.prevent="getList"
                        style="width: 250px">
                    </el-input>
                </div>

                <div class="ml-auto">
                    <el-button
                        type="primary"
                        @click="showForm = !showForm">
                        <i class="fas fa-plus"></i> Add New Process Sequence
                    </el-button>
                </div>
            </div>

            <div>
                <el-table
                    fit
                    :data="processSequences">
                    <el-table-column
                        v-for="column in columns"
                        :key="column.prop"
                        :sortable="column.sortable"
                        :show-overflow-tooltip="column.showOverflowTooltip"
                        :label="column.label"
                        :prop="column.prop">
                        <template slot-scope="scope">
                            <template v-if="column.prop === 'name'">
                                {{ $StringService.ucwords(scope.row[column.prop]) }}
                            </template>
                            <template v-else-if="column.prop === 'created_at'">
                                {{ scope.row[column.prop] | fixDateByFormat }}
                            </template>
                            <template v-else>
                                {{ scope.row[column.prop] }}
                            </template>
                        </template>
                    </el-table-column>

                    <el-table-column
                        label="Actions">
                        <template slot-scope="scope">
                            <el-button
                                @click="configureSequence(scope.row)"
                                type="text"
                                class="ml-2">
                                <i class="fas fa-cogs"></i>
                            </el-button>

                            <el-button
                                @click="stageProcessSequence(scope.row)"
                                type="text"
                                class="ml-2">
                                <i class="fas fa-pencil-alt"></i>
                            </el-button>

                            <el-popconfirm
                                @confirm="deleteProcessSequence(scope.row)"
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
                        :total="filters.total"
                        :page-size="filters.size"
                        :page-sizes="[10, 25, 50, 100]"
                        :current-page="filters.page"
                        @size-change="handleSize"
                        @current-change="handlePage">
                    </el-pagination>
                </div>

            </div>
        </div>

        <process-sequence-form
            :model="model"
            :mode="mode"
            :visible.sync="showForm"
            @close="closeForm">
        </process-sequence-form>
    </el-card>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from '../../mixins/pagination'

    export default {
        name: "ProcessSequence",
        mixins: [pagination],
        data() {
            let columns = [
                {prop: 'id', label: 'ID', showOverflowTooltip: true, sortable: true},
                {prop: 'name', label: 'Name', showOverflowTooltip: true, sortable: true},
                {prop: 'created_at', label: 'Created At', showOverflowTooltip: true, sortable: true},
            ]

            return {
                loading: false,
                showForm: false,
                columns: columns,
                processSequences: [],
                filters: {
                    searchString: null
                },
                model: null,
                mode: 'create'
            }
        },
        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'

            this.getList()

            this.$EventBus.listen('SETTINGS_PROCESS_SEQUENCES_CREATE', _ => {
                this.getList()
            })

            this.$EventBus.listen('SETTINGS_PROCESS_SEQUENCES_UPDATE', _ => {
                this.getList()
            })
        },
        methods: {
            getList() {
                this.loading = true

                this.$API.ProcessSequence.getList(this.filters)
                    .then(res => {
                        this.processSequences = res.data.data
                        this.filters.total = res.data.total
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            stageProcessSequence(processSequence) {
                this.showForm = true
                this.mode = 'update'
                this.model = cloneDeep(processSequence)
            },
            closeForm() {
                this.showForm = false
                this.model = null
                this.mode = 'create'
            },
            deleteProcessSequence(processSequence) {
                this.loading = true

                this.$API.ProcessSequence.delete(processSequence.id)
                    .then(res => {
                        this.getList()
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            configureSequence(processSequence) {
                window.location.href = `/admin/process-sequence/${processSequence.id}`
            }
        }
    }
</script>

<style scoped>

</style>
