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
            :placeholder="`Please select ${isMultiple ? 'employees' : 'an employee'}`"
            class="w-100">
            <el-option
                v-for="employee in employees"
                :key="employee.id"
                :value="employee.id"
                :label="employee.fullname | ucWords">
            </el-option>
        </el-select>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "GlobalEmployeeFilterSelector",

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
            ...mapGetters(['employees']),

            title() {
                return this.isMultiple ? 'Employees' : 'Employee'
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
