<template>
    <div>
        <el-card class="box-card mt-3">
        <div class="d-flex">
            <div>
                <h4>Welcome, {{ $root.user.name }}</h4>
            </div>

            <div class="ml-auto">
                <el-button>Logout</el-button>
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
                        type="primary">
                        <h5 class="card-title">{{ convertToDayName(slot.available_date) }}</h5>
                        <h5 class="card-text">{{ slot.available_date | fixDateTimeByFormat('DD, MMMM, YYYY') }}</h5>
                        <h5> {{ slot.working_hours }} Hours Available </h5>
                    </el-button>
                </div>
            </div>
        </div>
        </el-card>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    export default {
        name: "EmployeeAvailableBookingSlot",

        data() {
            return {
                days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
            }
        },

        created() {
            this.getAvailableSlots()
        },

        computed: {
            ...mapGetters('employeeOvertimeBooking', ['availableSlots'])
        },

        methods: {
            convertToDayName(dateString) {
                let date = new Date(dateString)
                return this.days[date.getDay()]
            },

            ...mapActions('employeeOvertimeBooking', ['getAvailableSlots'])
        }
    }
</script>
