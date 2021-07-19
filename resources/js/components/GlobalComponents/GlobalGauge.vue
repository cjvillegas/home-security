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

<style lang="scss">
    .gauge-container {
        height: 150px;
        width: 200px;

        .el-card__body {
            padding: 10px !important;
        }

        .gauge-counter {
            height: 115px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 900;
            font-size: 30px;
        }

        .gauge-title {
            font-size: 16px;
            text-align: center;
            font-weight: 900;
            line-height: 15px;
        }
    }
</style>
