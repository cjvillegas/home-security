<template>
    <div>
        <global-page-header title="Overtime Booking Slot"></global-page-header>

        <el-card class="box-card mt-3">
            <div>
                <div class="d-flex">
                    <div>

                    </div>

                    <div class="ml-auto">
                        <el-button
                            type="primary"
                            @click="openAddNewSlot">
                            <i class="fas fa-plus"></i> Add Slot
                        </el-button>
                    </div>
                </div>
            </div>

            <el-table
                v-loading="loading"
                :data="slots"
                fit>
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found">
                    </el-empty>
                </template>
                <el-table-column
                    prop="id"
                    label="ID"
                    sortable="">
                </el-table-column>
                <el-table-column
                    prop="available_date"
                    label="Date"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="working_hours"
                    label="Required Hours"
                    sortable>
                </el-table-column>
                <el-table-column
                    prop="is_locked"
                    label="Locked"
                    sortable="">
                    <template slot-scope="scope">
                        <template v-if="scope.row.is_locked">
                            Yes
                        </template>
                        <template v-else>
                            No
                        </template>
                    </template>
                </el-table-column>
                <el-table-column
                    width="100%"
                    label="Action/Status"
                    class-name="table-action-button">
                        <template slot-scope="scope">
                            <template>
                                <el-button
                                    type="primary"
                                    @click="toggle(scope.row.id)">
                                    <i v-bind:class="[scope.row.is_locked ? 'fa fa-lock' : 'fa fa-unlock']">
                                    </i>
                                </el-button>
                            </template>
                        </template>
                </el-table-column>
                <el-table-column
                    width="100%"
                    label="Delete Status"
                    class-name="table-action-button">
                        <template slot-scope="scope">
                            <template>
                               <el-popconfirm
                                    @confirm="deleteSlotAction(scope.row.id)"
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
            <el-pagination
                class="custom-pagination-class mt-3 mb-3 float-right"
                background
                layout="total, sizes, prev, pager, next"
                :total="pagination.total"
                :page-size="pagination.size"
                :page-sizes="[10, 25, 50, 100]"
                :current-page="pagination.page"
                @size-change="handleSize"
                @current-change="handlePage">
            </el-pagination>
        </el-card>

        <overtime-booking-form-dialog
            :visible.sync="showForm"
            @close="closeForm">
        </overtime-booking-form-dialog>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    import pagination from '../../mixins/pagination'
    export default {
        name: "OvertimeBookingIndex",
        mixins: [pagination],

        data() {
            return {
                filters: {
                    dateRange: null
                },
                showForm: false,
            }
        },

        created() {
            this.pagination.size = 10

            this.getSlotsAction()

            this.functionName = 'getSlotsAction'
        },

        computed: {
            ...mapGetters('overtimeBooking', ['slots', 'slotsTotal', 'loading'])
        },

        methods: {
            openAddNewSlot() {
                this.showForm = true
            },

            getSlotsAction() {
                let params = {...this.filters, ...this.pagination}
                console.log(params)
                this.getSlots(params)
            },

            toggle(id) {
                this.toggleLockSlot(id)
                .then((res) => {
                    this.$notify({
                        title: 'Success',
                        message: res.data.message,
                        type: 'success'
                    })
                    this.getSlots()
                })
            },

            deleteSlotAction(id) {
                this.deleteSlot(id)
                .then((res) => {
                    this.$notify({
                        title: 'Success',
                        message: res.data.message,
                        type: 'success'
                    })
                    this.getSlotsAction()
                })
            },

            closeForm() {
                this.showForm = false
            },

            ...mapActions('overtimeBooking', ['getSlots', 'saveSlot', 'toggleLockSlot', 'deleteSlot'])
        },

        watch: {
            slotsTotal: {
                handler() {
                    this.pagination.total = this.slotsTotal
                },
                immediate: true
            }
        }
    }
</script>
