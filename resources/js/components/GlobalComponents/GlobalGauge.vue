<template>
    <el-card class="box-card gauge-container">
        <div class="gauge-title">
            {{ title }}
        </div>

        <div class="gauge-counter">
            {{ count | numFormat}}
        </div>
    </el-card>
</template>

<script>
export default {
    name: "GlobalGauge",
    props: {
        count: {
            required: true,
            type: Number
        },
        title: {
            required: true,
            type: String
        }
    }
}
</script>
