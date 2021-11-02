<template>
    <div>
        <global-page-header title="Customers Management"></global-page-header>

        <el-card
            class="box-card mt-3">
            <div v-loading="loading">
                <div class="d-flex">
                    <div>
                        <el-input
                            v-model="filters.searchString"
                            clearable
                            placeholder="Search Customers..."
                            @keyup.enter.native.prevent="fetchCustomers"
                            style="width: 250px">
                        </el-input>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="stageCustomer(null)">
                            <i class="fas fa-plus"></i> Add Customer
                        </el-button>
                    </div>
                </div>

                <el-table
                    :data="customers"
                    class="w-100"
                    fit>
                    <template
                        slot="empty">
                        <el-empty
                            description="No Records Found">
                        </el-empty>
                    </template>
                    <el-table-column
                        prop="account_code"
                        label="Account Code"
                        sortable>
                    </el-table-column>
                    <el-table-column
                        prop="company_name"
                        label="Company Name"
                        sortable>
                    </el-table-column>
                    <el-table-column
                        prop="zoho_crm_id"
                        label="Zoho CRM ID"
                        sortable>
                    </el-table-column>
                    <el-table-column
                        width="100%"
                        label="Action"
                        class-name="table-action-button">
                        <template slot-scope="scope">
                            <template>

                                <el-button
                                @click="viewCustomer(scope.row)"
                                    type="text"
                                    class="ml-2 text-secondary">
                                    <i class="fas fa-eye"></i>
                                </el-button>

                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="Update Customer"
                                    :open-delay="500"
                                    placement="top">
                                    <el-button
                                        @click="stageCustomer(scope.row)"
                                        type="text"
                                        class="ml-2">
                                        <i class="fas fa-pencil-alt"></i>
                                    </el-button>
                                </el-tooltip>

                                <el-popconfirm
                                    @confirm="deleteCustomer(scope.row.id)"
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
        </el-card>

        <customer-form
            :model="model"
            :visible.sync="showForm"
            :is-view="view"
            @close="closeForm">
        </customer-form>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import { mapActions, mapGetters } from 'vuex'
    export default {
        data() {
            return {
                filters: {
                    searchString: null
                },
                model: null,
                view: false,
                showForm: false
            }
        },
        created() {
            this.fetchCustomers()

            this.$EventBus.listen('CUSTOMER_CREATE', _ => {
                this.fetchCustomers()
            })
        },
        computed: {
            ...mapGetters('customers', ['customers', 'loading'])
        },
        methods: {
            ...mapActions('customers', ['fetchCustomers', 'deleteCustomer']),
            viewCustomer(customer) {
                this.view = true
                this.model = cloneDeep(customer)
                this.showForm = true
            },
            stageCustomer(customer) {
                this.view = false
                this.model = cloneDeep(customer)
                this.showForm = true
            },
            closeForm() {
                this.model = null
                this.showForm = false
            },
        }
    }
</script>
