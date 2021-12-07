<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Teams List</h4>
        </el-card>
        <div v-loading="loading">
            <el-card class="box-card mt-3">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Teams..."
                            style="width: 250px"
                            @keyup.enter.native.prevent="fetchTeams">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="addNew">
                            <i class="fas fa-plus"></i> Add Team
                        </el-button>
                    </div>
                </div>

                <el-table
                    :data="teams"
                    class="w-100"
                    fit>

                    <el-table-column
                        prop="id"
                        label="ID"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        label="Name"
                        sortable>
                        <template slot-scope="scope">
                            {{ scope.row.name | ucWords }}
                        </template>
                    </el-table-column>

                    <el-table-column
                        prop="target"
                        label="Target"
                        sortable>
                    </el-table-column>

                    <el-table-column
                        width="100%"
                        label="Action"
                        class-name="table-action-button">
                        <template slot-scope="scope">
                            <el-tooltip
                                class="item"
                                effect="dark"
                                content="Configure Team"
                                placement="top"
                                :open-delay="1000">
                                <el-button
                                    @click="configureTeam(scope.row)"
                                    type="text"
                                    class="ml-2 text-secondary">
                                    <i class="fas fa-eye"></i>
                                </el-button>
                            </el-tooltip>

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
                                title="Are you sure to delete this Team?">
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
            </el-card>
        </div>
        <el-dialog
            :visible.sync="formDialogVisible"
            :title="dialogTitle"
            width="40%"
            @close="clearForm">
            <el-form
                v-loading="loading"
                ref="form"
                :model="form"
                :rules="rules">
                <el-form-item
                    label="Team Name"
                    prop="name"
                    :error="hasError('name')">
                    <el-input
                        v-model="form.name"
                        placeholder="Team Alpha"
                        clearable
                        class="w-100">
                    </el-input>
                </el-form-item>

                <el-form-item
                    label="Target"
                    prop="target"
                    :error="hasError('target')">
                    <el-input
                        v-model="form.target"
                        placeholder="1234"
                        clearable
                        class="w-100">
                    </el-input>
                </el-form-item>

                <el-form-item
                    label="Folders"
                    prop="folder_names">
                    <el-select
                        v-model="form.folder_names"
                        clearable
                        multiple
                        filterable
                        size="mini"
                        class="w-100">
                        <el-option
                            v-for="folder in folders"
                            :key="folder"
                            :value="folder"
                            :label="folder | ucWords">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <span
                slot="footer"
                class="dialog-footer"
                v-if="this.dialogType !== 'View'">
                <el-button
                    @click="clearForm">
                    Cancel
                </el-button>
                <el-button
                    type="primary"
                    @click="validate"
                    v-show="this.dialogType === 'Add'">
                    Save
                </el-button>
                <el-button
                    type="primary"
                    @click="validate"
                    v-show="this.dialogType === 'Edit'">
                    Update
                </el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import axios from "axios"
    import cloneDeep from "lodash/cloneDeep"
    import pagination from '../../mixins/pagination'
    import { formHelper } from '../../mixins/formHelper'

    export default {
        mixins: [pagination, formHelper],
        data() {
            return {
                loading: false,
                formDialogVisible: false,
                dialogType: 'Add',
                teams: [],
                filters: {
                    searchString: null,
                },
                form: this.getDefaultValues(),
                rules: {
                    name: {required: true, message: 'Name is required', trigger: ['blur', 'change']},
                },
                folders: []
            }
        },

        computed: {
            dialogTitle() {
                return (this.dialogType === 'Add') ? 'Add Team' : (this.dialogType === 'Edit') ? 'Edit Team' : 'View Team'
            }
        },

        created() {
            this.pagination.size = 10
            this.functionName = 'fetchTeams'
        },

        mounted() {
            this.fetchTeams()
            this.getFolders()
        },

        methods: {
            configureTeam(team) {
                this.$router.push({name: 'Team View', params: {team: team}})
            },

            getFolders() {
                this.loading = true

                axios.get('/admin/teams/folders')
                    .then(res => {
                        this.folders = cloneDeep(res.data)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            fetchTeams() {
                this.loading = true

                let params = {...this.filters, ...this.pagination}

                this.$API.Team.getList(params)
                .then((response) => {
                    this.teams = response.data.teams.data
                    this.pagination.total = response.data.teams.total
                })
                .catch((err) => {
                    console.log(err)
                })
                .finally(() => {
                    this.loading = false
                })
            },

            addNew() {
                if(this.dialogType === 'Edit') {
                    this.clearForm()
                }
                this.dialogType = 'Add'
                this.formDialogVisible = true
            },

            saveTeam() {
                this.loading = true

                this.$API.Team.save(this.form)
                .then((response) => {
                    switch(response.status){
                        case 200:
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchTeams()
                            this.clearForm()
                    }
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        this.setErrors(err.response.data.errors)
                    }
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            updateTeam() {
                this.loading = true

                this.$API.Team.update(this.form)
                .then((response) => {
                    switch(response.status){
                        case 200:
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            })
                            this.fetchTeams()
                            this.clearForm()
                    }
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        this.setErrors(err.response.data.errors)
                    }
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            validate() {
                this.$refs.form.validate(valid => {
                    if (valid) {
                        this.resetErrors()
                        if (this.dialogType === 'Edit') {
                            this.updateTeam()

                            return
                        }
                        this.saveTeam()
                    }
                })
            },

            openEditDialog(item) {
                this.dialogType = 'Edit'
                this.form.id = item.id
                this.form.name = item.name
                this.form.target = item.target
                this.form.folder_names = item.folder_names
            },

            deleteTeam(id) {
                this.$API.Team.delete(id)
                .then( (response) => {
                    this.$notify({
                        title: 'Deleted!',
                        message: response.data.message,
                        type: 'success'
                    });
                    this.fetchTeams()
                })
            },

            clearForm() {
                if (this.$refs.form) {
                    this.$refs.form.clearValidate()
                }

                this.form = this.getDefaultValues()

                this.formDialogVisible = false
            },

            getDefaultValues() {
                return {
                    id: null,
                    name: null,
                    target: null,
                    folder_names: []
                }
            }
        }
    }
</script>
