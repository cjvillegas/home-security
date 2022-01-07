<template>
    <el-dialog
        :visible.sync="showDialog"
        title="View Employee Overtime"
        :before-close="closeForm">

        <div
            class="row"
            v-loading="loading">
            <div
                v-for="overtime in employee.overtime_slots"
                :key="overtime.id"
                class="col-md-3">

                <div
                    v-if="overtime.checked_by == null"
                    class="card bg-danger w-250">
                    <div class="card-body">
                        <p class="text-center"> {{ overtime.overtime_booking.available_date | fixDateTimeByFormat('dddd') }} </p>
                        <p class="text-center"> {{ overtime.overtime_booking.available_date | fixDateTimeByFormat('MMMM Do YYYY') }} </p>
                        <p class="text-center"> {{ overtime.overtime_booking.working_hours }} Hours available </p>
                    </div>
                </div>
                <div v-if="overtime.checked_by != null">
                    <div
                        v-if="overtime.is_approved"
                        class="card text-white bg-success w-250">
                        <div class="card-body">
                            <p class="text-center"> Approved By: {{ overtime.checked_by.name }} </p>
                            <p class="text-center"> Approved Date: {{ overtime.overtime_booking.available_date | fixDateTimeByFormat('MMMM Do YYYY') }} </p>
                            <p class="text-center"> Approved Hours: {{ overtime.overtime_booking.working_hours }} </p>
                        </div>
                    </div>
                    <div
                        v-else
                        class="card text-white bg-danger w-250">
                        <div class="card-body">
                            <p class="text-center"> Rejected By: {{ overtime.checked_by.name }} </p>
                            <p class="text-center"> Rejected Date: {{ overtime.overtime_booking.available_date | fixDateTimeByFormat('MMMM Do YYYY') }} </p>
                            <p class="text-center"> Hours: {{ overtime.overtime_booking.working_hours }} </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<style scoped>

</style>
<script>
    import { mapActions, mapGetters } from 'vuex'
    import {dialog} from "../../../mixins/dialog"
    import {formHelper} from "../../../mixins/formHelper"
    export default {
        name: "ViewEmployeeOvertime",
        mixins: [dialog, formHelper],
        props: {
            employee: {}
        },

        data() {
            return {
                loading: false
            }
        },

        created() {

        },

        computed: {

        },

        methods: {
            closeForm() {
                this.closeModal()
            },

            ...mapActions('overtimeBooking', ['showEmployeeOvertimeList'])
        }
    }
</script>
