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
            title: {
                required: true,
                type: String
            }
        }
    }
</script>
