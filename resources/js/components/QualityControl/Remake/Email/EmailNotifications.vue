<template>
    <div>
        <global-page-header title="Email Notifications"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                 <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search Email..."
                        @keyup.enter.native.prevent="getEmails(this.filters)"
                        style="width: 250px">
                    </el-input>
                </div>
                <div class="ml-auto">
                     <el-button
                        type="primary"
                        @click="addNew">
                        <i class="fas fa-plus"></i> Add Email
                    </el-button>
                </div>
            </div>
            <el-table
                :data="emails"
                class="w-100"
                fit>
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found...">
                    </el-empty>
                </template>
                <el-table-column
                    prop="name"
                    label="Name">
                </el-table-column>

                <el-table-column
                    prop="email"
                    label="Email">
                </el-table-column>

                <el-table-column
                    width="100%"
                    label="Action"
                    class-name="table-action-button">
                    <template slot-scope="scope">
                        <template>
                            <el-popconfirm
                                @confirm="deleteEmail(scope.row.id)"
                                confirm-button-text='OK'
                                cancel-button-text='No, Thanks'
                                icon="el-icon-info"
                                icon-color="red"
                                :title="confirmDeleteTitle(scope.row.name)">
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
        </el-card>

        <email-form
            :visible.sync="showForm"
            @close="closeForm">
        </email-form>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    export default {
        name: "EmailNotifications",
        data() {
            return {
                filters: {},
                showForm: false
            }
        },
        created() {
            this.getEmails()
        },
        computed: {
            ...mapGetters('remakeChecker', ['emails', 'loading'])
        },
        methods: {
            ...mapActions('remakeChecker', ['getEmails', 'deleteEmail']),
            confirmDeleteTitle(name) {
                return `Are you sure you want to remove the following user: ${name} to receive Email Notification when a new report is created?`
            },
            addNew() {
                this.showForm = true
            },
            closeForm() {
                this.showForm = false
            },
        }
    }
</script>
