<template>
    <div>
        <label v-if="showLabel">{{ title }}</label>
        <el-select
            v-model="model"
            @change="handleChange"
            filterable
            clearable
            :multiple="isMultiple"
            :collapse-tags="isMultiple"
            :placeholder="`Please select ${isMultiple ? 'quality controls' : 'a quality control'}`"
            class="w-100">
            <el-option
                v-for="qualityControl in qualityControls"
                :key="qualityControl.id"
                :value="qualityControl.id"
                :label="qualityControl.qc_code | ucWords">
            </el-option>
        </el-select>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "GlobalQualityControlSelector",

        props: {
            isMultiple: {
                type: Boolean,
                default: false,
                required: true
            },
            showLabel: {
                type: Boolean,
                default: true
            },
            value: {}
        },

        data() {
            return {
                model: this.isMultiple ? [] : null
            }
        },

        computed: {
            ...mapGetters(['qualityControls']),

            title() {
                return this.isMultiple ? 'Quality Controls' : 'Quality Control'
            }
        },

        methods: {
            handleChange() {
                this.$emit('update:value', this.model)
            }
        },

        watch: {
            value: {
                handler() {
                    this.model = this.value
                },
                immediate: true
            }
        }
    }
</script>
