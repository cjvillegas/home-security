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
                <el-table-column>

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

            closeForm() {
                this.showForm = false
            },

            ...mapActions('overtimeBooking', ['getSlots', 'saveSlot'])
        },
    }
</script>
