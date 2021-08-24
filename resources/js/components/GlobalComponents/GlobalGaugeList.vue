<template>
    <el-card class="box-card gauge-container">
        <div class="gauge-title">
            {{ title }}
        </div>
        <div class="mt-4">
            <div
                v-for="row in data"
                :key="row.key">
                <div class="d-flex f-size-16">
                    <span class="font-weight-bolder">{{ row.label }}:</span>
                    <span class="ml-auto">{{ row.count | numFormat}}</span>
                </div>
            </div>
            <div
                v-for="machine in machines"
                :key="machine.id">
                <div class="d-flex f-size-16">
                    <span class="font-weight-bolder">{{ machine.name }}:</span>
                    <span class="ml-auto">{{ totalBox(shift, machine.id)}}</span>
                </div>
            </div>
        </div>
    </el-card>
</template>

<script>
    export default {
        name: "GlobalGaugeList",
        props: {
            data: {
                required: true,
                type: Array
            },
            machines: {
                required: true,
            },
            machineData: {
                required: true,
            },
            shift: {
                required: false,
            },
            title: {
                required: true,
                type: String
            }
        },

        methods: {
            totalBox(shift_id, machine_id) {
                let filteredItems = []

                filteredItems = this.machineData.filter((machine) => {
                    return machine.shift_id == shift_id && machine.id == machine_id
                })

                if(this.title == 'Total') {
                    filteredItems = this.machineData.filter((machine) => {
                        return machine.id == machine_id
                    })
                }

                if(filteredItems.length != 0) {
                    return filteredItems[0].boxes
                }else {
                    return 0;
                }
            }
        }
    }
</script>
