<template>
    <div class="mt-3">
        <b>Filters:</b>
        <div>
            <el-tag
                v-for="(visual, index) in selectedFiltersVisual"
                :key="index"
                type="info"
                style="
                    max-width: 500px;
                    min-width: 10px;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                "
                :class="`${index > 0 ? 'ml-2' : ''}`">
                {{ visual }}
            </el-tag>
        </div>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep";
    import {mapGetters} from "vuex";

    export default {
        name: "WorkAnalyticsFilterVisual",

        props: {
            filters: {
                required: true,
                type: Object
            }
        },

        data() {
            return {}
        },

        computed: {
            ...mapGetters(['employees', 'shifts', 'processes']),

            selectedFiltersVisual() {
                let filters = cloneDeep(this.filters)
                let visual = []

                if (filters.date) {
                    if (Array.isArray(filters.date)) {
                        let sod = moment(filters.date[0]).format('MMM DD, YYYY')
                        let eod = moment(filters.date[1]).format('MMM DD, YYYY')
                        visual.push(`Date: ${sod} - ${eod}`)
                    } else {
                        visual.push(`Date: ${moment(filters.date).format('MMM DD, YYYY')}`)
                    }
                }

                if (filters.shift !== undefined) {
                    let shiftVisual = "Shift:"
                    if (!filters.shift) {
                        visual.push(`${shiftVisual} All`)
                    } else {
                        let shift = this.shifts.find(sh => sh.id === this.filters.shift)

                        if (shift) {
                            visual.push(`${shiftVisual} ${shift.name}`)
                        }
                    }
                }

                if (filters.employees && Array.isArray(filters.employees)) {
                    let employeeVisual = "Employee:"
                    if (filters.employees.every(em => em === null)) {
                        visual.push(`${employeeVisual} All`)
                    } else {
                        visual.push(employeeVisual + " " + filters.employees.reduce((acc, cur) => {
                            let employee = this.employees.find(em => em.barcode === cur)

                            if (employee) {
                                acc += acc ? `, ${employee.fullname}` : employee.fullname
                            }

                            return acc
                        }, ''))
                    }
                }

                if (filters.processes && Array.isArray(filters.processes)) {
                    let processVisual = "Process:"
                    if (filters.processes.every(em => em === null)) {
                        visual.push(`${processVisual} All`)
                    } else {
                        visual.push(processVisual + " " + filters.processes.reduce((acc, cur) => {
                            let process = this.processes.find(em => em.barcode === cur)

                            if (process) {
                                acc += acc ? `, ${process.name}` : process.name
                            }

                            return acc
                        }, ''))
                    }
                }



                return visual
            }
        }
    }
</script>
