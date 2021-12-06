<template>
    <div>
        <div class="d-flex">
            <div>
                 <el-button
                    @click="backToFilters"
                    type="primary">
                    <i class="fas fa-arrow-left"></i> Back
                </el-button>
            </div>
            <div class="ml-auto">
                <el-button>
                    <i class="fa fa-calendar"></i>
                    {{ form.dateRange[0] | fixDateTimeByFormat('MMM DD, YYYY') }} ~ {{ form.dateRange[1] | fixDateTimeByFormat('MMM DD, YYYY') }}
                </el-button>
            </div>
        </div>
        <div class="d-flex mt-2">
            <div class="ml-auto">
                <el-button
                    @click="clickExport">
                    <i class="fa fa-download"></i>
                    Export
                </el-button>
            </div>
        </div>
        <div
            v-for="(shiftPerformance, shiftPerformanceKey) in shiftPerformances"
            :key="shiftPerformanceKey"
            class="table table-responsive mt-2">
            <h4> Department: {{ shiftPerformance.department }} | Shift: {{ shiftPerformance.shift }}</h4>
            <table
                v-if="shiftPerformance.department != 'Despatch'"
                class="table table-bordered">
                <thead>
                    <tr class="table-primary">
                        <th>Date Manufactured</th>
                        <th>Fully Manufactured</th>
                        <th>Date Planned</th>
                        <th>Total Planned</th>
                        <th>People Worked</th>
                        <th>Target Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                    v-for="(departmentData, departmentDataKey) in shiftPerformance.data"
                    :key="departmentDataKey">
                        <td>{{ departmentData.date }}</td>
                        <td>{{ departmentData.fully_manufactured }}</td>
                        <td>{{ departmentData.date }}</td>
                        <td>{{ departmentData.total_planned }}</td>
                        <td>{{ departmentData.people_worked }}</td>
                        <td v-bind:class="[(departmentData.target_performance['value'] == 0) ? 'bg-info' : departmentData.target_performance['value'] > 0 ? 'bg-warning' : 'bg-success']" style="color: black">
                            {{ departmentData.target_performance['message'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <table
                v-else
                class="table table-bordered">
                <thead>
                    <tr class="table-primary">
                        <th>Date Manufactured</th>
                        <th>Machine Packed</th>
                        <th>Headrail Packed</th>
                        <th>Louvres Packed</th>
                        <th>People Worked</th>
                    </tr>
                </thead>
                <tr
                    v-for="(departmentData, departmentDataKey) in shiftPerformance.data"
                    :key="departmentDataKey">
                        <td>{{ departmentData.date }}</td>
                        <td>{{ departmentData.machine_packed }}</td>
                        <td>{{ departmentData.headrail_packed }}</td>
                        <td>{{ departmentData.louvres_packed }}</td>
                        <td>{{ departmentData.people_worked }}</td>
                    </tr>
            </table>
        </div>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    export default {
        name: "ShiftPerformanceView",
        data() {
            return {

            }
        },

        computed: {
            ...mapGetters('shiftPerformance', ['shiftPerformances', 'form'])
        },

        methods: {
            clickExport() {
                this.exportShiftPerformances(this.form)
                .then(() => {
                    this.$notify({
                        title: 'Success',
                        message: 'Your data is being exported. Please wait a while and check the Export page for your export',
                        type: 'success'
                    })
                })
            },

            ...mapActions('shiftPerformance', ['backToFilters', 'exportShiftPerformances'])
        }
    }
</script>
