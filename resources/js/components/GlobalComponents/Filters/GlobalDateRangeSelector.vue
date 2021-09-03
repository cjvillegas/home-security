<template>
    <div>
        <label v-if="showLabel">Select Dates</label>
        <el-date-picker
            v-model="model"
            @change="handleChange"
            type="daterange"
            range-separator="~"
            start-placeholder="Start date"
            end-placeholder="End date"
            class="w-100">
        </el-date-picker>
    </div>
</template>

<script>
    export default {
        name: "GlobalDateRangeSelector",

        props: {
            showLabel: {
                type: Boolean,
                default: true
            },
            value: {}
        },

        data() {
            return {
                model: []
            }
        },

        methods: {
            handleChange() {
                this.$emit('update:value', this.model)
            }
        },
    }
</script>
