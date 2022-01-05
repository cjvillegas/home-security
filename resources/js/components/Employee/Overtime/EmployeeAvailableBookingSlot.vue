<template>
    <div>
        <el-card
            class="box-card mt-3"
            v-loading="loading">
            <div class="d-flex">
                <div>
                    <h4>Welcome, {{ employee.fullname }}</h4>
                </div>

                <div class="ml-auto">
                    <el-button
                        @click="refresh">
                        <i class="fa fa-refresh">
                            Refresh
                        </i>
                    </el-button>
                    <el-button
                        @click="reset">
                        Logout
                    </el-button>
                </div>
            </div>
            <p class="text-center">Please selected Date(s) for which you're available for Overtime.</p>

            <div class="row">
                <div
                    v-for="slot in availableSlots"
                    :key="slot.id"
                    class="col-md-3">
                    <div class="card" style="width: 18rem;">
                        <el-button
                            @click="addSelectedSlot(slot)"
                            :disabled="isInArray(slot.id, employeeConfirmedSlots, true)"
                            :type="isSelected(slot.id, slot.is_locked)">
                            <h5 class="card-title">{{ convertToDayName(slot.available_date) }}</h5>
                            <h5 class="card-text">{{ slot.available_date | fixDateTimeByFormat('DD, MMMM, YYYY') }}</h5>
                            <h5> {{ slot.working_hours }} Hours Available </h5>
                        </el-button>
                    </div>
                </div>
            </div>

            <div
                v-show="this.payload.selectedSlots.length > 0"
                class="alert alert-warning" role="alert">

                <p class="text-center">You have selected</p>
                <p
                    class="text-center"
                    v-for="slot in payload.selectedSlots"
                    :key="slot.id">
                    {{ convertToDayName(slot.available_date) }} {{ slot.available_date | fixDateTimeByFormat('DD, MMMM, YYYY') }}
                    - Attendance {{ slot.working_hours }} Hours
                </p>

                <div class="d-flex">
                    <div>
                    </div>

                    <div class="ml-auto">
                        <el-button
                            @click="confirmSelectedSlots"
                            type="info"
                            icon="el-icon-circle-check">
                            Confirm
                        </el-button>
                    </div>
                </div>
            </div>

            <h3>Your Approvals</h3>
            <el-table
                :data="employee.overtime_slots"
                class="w-100"
                fit>
                <template
                    slot="empty">
                    <el-empty
                        description="No Records Found">
                    </el-empty>
                </template>

                <el-table-column
                    prop="overtime_booking.available_date"
                    label="Available Slot"
                    sortable>
                    <template
                        slot-scope="scope">
                        {{ scope.row.overtime_booking.available_date | fixDateTimeByFormat('dddd, DD, MMMM, YYYY') }}
                    </template>
                </el-table-column>

                <el-table-column
                    label="Confirmation Given"
                    sortable>
                     <template
                        slot-scope="scope">
                        <template
                            v-if="scope.row.is_approved">
                            {{ scope.row.approved_at | fixDateTimeByFormat('DD, MMMM, YYYY') }}
                        </template>

                        <template v-else>
                            Not Confirmed
                        </template>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="overtime_booking.available_date"
                    label="Working Date"
                    sortable>
                    <template
                        slot-scope="scope">
                        {{ scope.row.overtime_booking.available_date | fixDateTimeByFormat('dddd, DD, MMMM, YYYY') }}
                    </template>
                </el-table-column>

                <el-table-column
                    prop="overtime_booking.shift"
                    label="Shift"
                    sortable>
                    <template
                        slot-scope="scope">
                        <template
                            v-if="scope.row.overtime_booking.shift == 'morning'">
                            Morning (06-14)
                        </template>
                        <template
                            v-if="scope.row.overtime_booking.shift == 'afternoon'">
                            Afternoon (14-22)
                        </template>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="department"
                    label="Department"
                    sortable>
                    <template
                        slot-scope="scope">
                        {{ scope.row.department | ucWords }}
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
    import { mapActions, mapGetters, mapMutations } from 'vuex'
    export default {
        name: "EmployeeAvailableBookingSlot",

        data() {
            return {
                days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                payload: {
                    barcode: null,
                    selectedSlots: []
                },
            }
        },

        created() {
            this.getAvailableSlots()
        },

        computed: {
            ...mapGetters('employeeOvertimeBooking', ['availableSlots', 'barcode', 'employee', 'employeeConfirmedSlots', 'loading'])
        },

        methods: {
            addSelectedSlot(slot) {
                console.log(slot)
                if (this.isInArray(slot.id, this.payload.selectedSlots)) {
                    let obj = this.payload.selectedSlots.find(item => item.id == slot.id)
                    let index = this.payload.selectedSlots.indexOf(obj)
                    if (index !== -1) {
                        console.log(index)
                        this.payload.selectedSlots.splice(index, 1);
                    }

                    return
                }

                this.payload.selectedSlots.push(slot)
            },

            confirmSelectedSlots() {
                this.$confirm('Confirm booking for these Selected Slots. Continue?', 'Warning', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                }).then(() => {
                    this.payload.barcode = this.employee.barcode

                    this.setLoading(true)
                    this.saveSelectedSlots(this.payload)
                    .then((res) => {
                        this.$message({
                            type: 'success',
                            message: res.data.message
                        });

                        this.setLoading(false)
                        this.setSelectedSlots(this.payload.selectedSlots)
                        setTimeout(_ => {
                            this.setPage('sucessForm')
                        }, 100)
                    })

                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Action cancelled'
                    });
                });
            },

            isSelected(id, is_locked = false) {
                if (is_locked == true) {
                    return 'danger'
                }
                return this.isInArray(id, this.payload.selectedSlots) ? 'info' : 'primary'
            },

            isInArray(value, array, isAlreadyConfirmed = false) {

                if (isAlreadyConfirmed) {
                    let obj = array.find(item => item.overtime_booking_id == value)
                    return obj != null
                }
                let obj = array.find(item => item.id == value)
                return obj
            },

            convertToDayName(dateString) {
                let date = new Date(dateString)
                return this.days[date.getDay()]
            },

            refresh() {
                this.getAvailableSlots()

                this.enterBarcode(this.barcode)
                .then((res) =>{
                    if (res.status == 200) {
                        this.setEmployee(res.data.employee)
                        this.setEmployeeConfirmedSlots(res.data.employee.confirmed_slots)
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                })

                this.payload = {
                    barcode: null,
                    selectedSlots: []
                }
            },

            ...mapActions('employeeOvertimeBooking', ['getAvailableSlots', 'saveSelectedSlots', 'enterBarcode', 'reset']),
            ...mapMutations('employeeOvertimeBooking', ['setPage', 'setEmployee', 'setEmployeeConfirmedSlots', 'setLoading', 'setSelectedSlots'])
        }
    }
</script>
