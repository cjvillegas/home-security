<template>
    <div>
        <global-page-header title="Employee Overtime Booking"></global-page-header>

        <el-card class="box-card mt-3" v-if="page == 'welcome'">
            <div class="px-4 py-5 my-5 text-center">
                <h1 class="display-5 fw-bold">Welcome</h1>
                <div class="col-lg-6 mx-auto">
                    <p class="lead mb-4">Please scan your Employee Barcode to begin.</p>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <el-input
                            placeholder="E100"
                            @keyup.enter.native.prevent="verifyBarcode"
                            v-model="barcode"
                            size="small"
                            clearable
                            class="barcode-input">
                        </el-input>
                    </div>

                    <div class="mt-5">
                        <div class="alert alert-warning" role="alert">
                            Please try to confirm your attendance to weekend working within 48 hours before Friday.
                            Preferred will be for you to confirm your attendance Monday, Tuesday, Wednesday.
                        </div>
                    </div>
                </div>
            </div>
        </el-card>

        <employee-available-booking-slot
            v-if="page == 'bookingSlot'">
        </employee-available-booking-slot>

        <employee-overtime-success-dialog
            v-if="page == 'sucessForm'">
        </employee-overtime-success-dialog>
    </div>
</template>

<style scoped>
    .barcode-input {
        width: 200px !important;
    }
</style>
<script>
    import { mapActions, mapMutations, mapGetters } from 'vuex'
    export default {
        name: "EmployeeOvertimeIndex",

        data() {
            return {
                barcode: null
            }
        },

        computed: {
            ...mapGetters('employeeOvertimeBooking', ['page'])
        },

        created() {
            setTimeout(_ => {
                console.log('asdasdasd')
            },5000)
        },

        methods: {
            verifyBarcode() {
                this.setLoading(true)

                this.enterBarcode(this.barcode)
                .then((res) =>{
                    if (res.status == 201) {
                        this.$message({
                            type: 'warning',
                            message: res.data.message
                        });
                    }

                    if (res.status == 200) {
                        this.setBarcode(this.barcode),
                        this.setEmployee(res.data.employee)

                        this.setEmployeeConfirmedSlots(res.data.employee.confirmed_slots)
                        this.setPage('bookingSlot')
                    }
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.setLoading(false)
                })

            },
            ...mapActions('employeeOvertimeBooking', ['enterBarcode']),
            ...mapMutations('employeeOvertimeBooking', ['setBarcode', 'setEmployee', 'setEmployeeConfirmedSlots', 'setLoading', 'setPage'])
        }
    }
</script>
