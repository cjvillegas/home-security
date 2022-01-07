<template>
    <div>
        <el-card class="box-card mt-3"
            v-loading="loading">
            <div class="d-flex">
                <div>

                </div>

                <div class="ml-auto">
                    <el-button
                        @click="reset">
                        Logout
                    </el-button>
                </div>
            </div>
            <div class="px-4 py-5 my-5 text-center">
                <span class="d-flex justify-content-center text-sucess">
                    <i class="fa fa-check-circle-o fa-5x" aria-hidden="true"></i>
                </span>
                <div
                    v-for="(slot, slotKey) in selectedSlots"
                    :key="slotKey"
                    class="mb-5">
                    <p class="h3 text-center">
                        Thank you for confirming of Working
                        {{ convertToDayName(slot.available_date) }} {{ slot.available_date | fixDateTimeByFormat('DD, MMMM, YYYY') }}
                            - Attendance {{ slot.working_hours }} Hours
                    </p>
                </div>
                <div class="col-lg-6 mx-auto">
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <p class="h4 text-center text-info">
                            If you will be selected to work, the Shift Leaders will confirm this to you, you can also check this page for confirmation
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 mx-auto mt-5">
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <p class="h5 text-center">
                            This page will log off in the next 15 seconds
                        </p>
                    </div>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    export default {
        data() {
            return {
                days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            }
        },

        computed: {
            ...mapGetters('employeeOvertimeBooking', ['loading', 'selectedSlots'])
        },

        created() {
            setTimeout(_ => {
                location.reload(true);
            }, 15000)
        },

        methods: {
            convertToDayName(dateString) {
                let date = new Date(dateString)
                return this.days[date.getDay()]
            },
            ...mapActions('employeeOvertimeBooking', ['reset'])
        }
    }
</script>
