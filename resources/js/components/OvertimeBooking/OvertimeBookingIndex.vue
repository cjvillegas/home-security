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
            </el-table>
        </el-card>

        <overtime-booking-form-dialog
            :visible.sync="showForm"
            @close="closeForm">
        </overtime-booking-form-dialog>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'

    export default {
        name: "OvertimeBookingIndex",
        data() {
            return {
                filters: {
                    dateRange: null
                },
                showForm: false,
            }
        },

        mounted() {
            this.getSlots(this.filters)
        },

        computed: {
            ...mapGetters('overtimeBooking', ['slots'])
        },

        methods: {
            openAddNewSlot() {
                this.showForm = true
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

            closeForm() {
                this.showForm = false
            },

            ...mapActions('overtimeBooking', ['getSlots', 'saveSlot', 'toggleLockSlot'])
        },
    }
</script>
