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
            :placeholder="`Please select ${isMultiple ? 'processes' : 'a process'}`"
            class="w-100">
            <el-option
                v-for="process in processes"
                :key="process.id"
                :value="process.id"
                :label="process.name | ucWords">
            </el-option>
        </el-select>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "GlobalProcessFilterSelector",

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
            ...mapGetters(['processes']),

            title() {
                return this.isMultiple ? 'Processes' : 'Process'
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
