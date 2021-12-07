<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">User List</h4>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search users..."
                        @keyup.enter.native.prevent="getList"
                        style="width: 250px">
                    </el-input>
                </div>

                <div class="ml-auto">
                    <el-popover
                        placement="bottom-start"
                        width="350"
                        trigger="click"
                        class="mr-2">
                        <label>Status</label>
                        <el-select
                            v-model="filters.status"
                            class="w-100">
                            <el-option label="Active" :value="1"></el-option>
                            <el-option label="Deactivated" :value="0"></el-option>
                            <el-option label="Show All" :value="null"></el-option>
                        </el-select>

                        <el-button
                            @click="getList"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>

                        <el-button
                            slot="reference">
                            <i class="fas fa-filter"></i>
                        </el-button>
                    </el-popover>

                    <el-button
                        v-if="permissions.user_create"
                        type="primary"
                        @click="stageUser(null)">
                        <i class="fas fa-plus"></i> Add User
                    </el-button>
                </div>
            </div>

            <el-table
                fit
                :data="users">
                <el-table-column
                    v-for="column in columns"
                    :key="column.prop"
                    :sortable="column.sortable"
                    :show-overflow-tooltip="column.showOverflowTooltip"
                    :label="column.label"
                    :prop="column.prop">
                    <template slot-scope="scope">
                        <template v-if="column.prop === 'id'">
                            {{ scope.row[column.prop] | numFormat }}
                        </template>
                        <template v-else-if="column.prop === 'name'">
                            {{ scope.row[column.prop] | ucWords }}
                        </template>
                        <template v-else-if="column.prop === 'email_verified_at'">
                            {{ scope.row[column.prop] | fixDateTimeByFormat }}
                        </template>
                        <template v-else-if="column.prop === 'is_active'">
                            <el-tag
                                size="mini"
                                :type="scope.row.is_active ? 'success' : 'danger'"
                                effect="dark">
                                {{ scope.row.is_active ? 'Active' : 'Deactivated' }}
                            </el-tag>
                        </template>
                        <template v-else-if="column.prop === 'roles'">
                            <el-tag
                                v-for="role in scope.row.roles"
                                :key="role.id"
                                type="primary"
                                class="m-2">
                                {{ role.title | ucWords }}
                            </el-tag>
                        </template>
                        <template v-else>
                            {{ scope.row[column.prop] }}
                        </template>
                    </template>
                </el-table-column>

                <el-table-column
                    label="Actions"
                    width="200">
                    <template slot-scope="scope">
                        <el-tooltip
                            v-if="permissions.user_show"
                            class="item"
                            effect="dark"
                            content="View User Information"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                @click="viewUser(scope.row)"
                                type="text"
                                class="ml-2 text-secondary">
                                <i class="fas fa-eye"></i>
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            v-if="permissions.user_edit"
                            class="item"
                            effect="dark"
                            content="Update User"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                @click="stageUser(scope.row)"
                                type="text"
                                class="ml-2">
                                <i class="fas fa-pencil-alt"></i>
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            v-if="permissions.user_status_change"
                            class="item"
                            effect="dark"
                            :content="`${scope.row.is_active ? 'Deactivate' : 'Activate'} User`"
                            :open-delay="500"
                            placement="top">
                            <el-popconfirm
                                @confirm="changeStatus(scope.row)"
                                confirm-button-text='OK'
                                cancel-button-text='No, Thanks'
                                icon="el-icon-info"
                                icon-color="red"
                                :title="`Are you sure to ${scope.row.is_active ? 'deactivate' : 'activate'} this user?`">
                                <el-button
                                    type="text"
                                    class="text-success ml-2"
                                    slot="reference">
                                    <i
                                        v-if="scope.row.is_active"
                                        class="fas fa-user-alt-slash">
                                    </i>
                                    <i
                                        v-else
                                        class="fas fa-user-plus">
                                    </i>
                                </el-button>
                            </el-popconfirm>
                        </el-tooltip>

                        <el-tooltip
                            v-if="permissions.user_delete"
                            class="item"
                            effect="dark"
                            content="Delete User"
                            :open-delay="500"
                            placement="top">
                            <el-popconfirm
                                @confirm="deleteUser(scope.row)"
                                confirm-button-text='OK'
                                cancel-button-text='No, Thanks'
                                icon="el-icon-info"
                                icon-color="red"
                                title="Are you sure to delete this user?">
                                <el-button
                                    type="text"
                                    class="text-danger ml-2"
                                    slot="reference">
                                    <i class="fas fa-trash-alt"></i>
                                </el-button>
                            </el-popconfirm>
                        </el-tooltip>
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
        </el-card>

        <user-form
            :model="model"
            :visible.sync="showForm"
            :roles="pageData.roles"
            @close="closeForm">
        </user-form>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from '../../mixins/pagination'

    export default {
        name: "UserList",
        mixins: [pagination],
        props: {
            pageData: {
                type: Object,
                required: true
            }
        },
        data() {
            let columns = [
                {label: 'ID', prop: 'id', showOverflowTooltip: true, sortable: true},
                {label: 'Name', prop: 'name', showOverflowTooltip: true, sortable: true},
                {label: 'Email', prop: 'email', showOverflowTooltip: true, sortable: true},
                {label: 'Status', prop: 'is_active', showOverflowTooltip: true, sortable: true},
                {label: 'Email Verified At', prop: 'email_verified_at', showOverflowTooltip: true, sortable: true},
                {label: 'Roles', prop: 'roles'},
            ]

            return {
                loading: false,
                showForm: false,
                permissions: this.pageData.permissions,
                filters: {
                    searchString: null,
                    status: 1
                },
                users: [],
                columns: columns,
                model: null
            }
        },
        created() {
            // define the function name that will be called when any
            // property form the pagination changed
            this.functionName = 'getList'

            this.getList()

            this.$EventBus.listen('USER_CREATE', _ => {
                this.getList()
            })

            this.$EventBus.listen('USER_UPDATE', _ => {
                this.getList()
            })
        },
        methods: {
            getList() {
                this.loading = true

                let params = {...this.filters, ...this.pagination}

                this.$API.User.getList(params)
                    .then(res => {
                        this.users = res.data.data
                        this.pagination.total = res.data.total
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            viewUser(user) {
                this.$router.push({name: 'User View', params: {id: user.id}})
            },
            stageUser(user) {
                this.model = cloneDeep(user)
                this.showForm = true
            },
            changeStatus(user) {
                let status = user.is_active ? 'Deactivate' : 'Activate'

                this.loading = true

                this.$API.User.changeStatus(user.id)
                    .then(res => {
                        if (res.data) {

                            this.getList()
                            this.$notify({
                                title: 'Success',
                                message: `User successfully ${status}.`,
                                type: 'success'
                            })
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            deleteUser(user) {
                this.loading = true

                this.$API.User.delete(user.id)
                    .then(res => {
                        if (res.data) {
                            this.getList()

                            this.$notify({
                                title: 'Success',
                                message: `User successfully deleted.`,
                                type: 'success'
                            })
                        }
                    })
                    .catch(err => {
                        console.log(err)
                        this.loading = false
                    })
            },
            closeForm() {
                this.model = null
                this.showForm = false
            }
        }
    }
</script>

<style scoped>

</style>
