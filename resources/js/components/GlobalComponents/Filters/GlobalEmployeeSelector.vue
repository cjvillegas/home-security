<template>
    <div>
        <label
            v-if="showLabel">
            {{ title }}
            <el-tooltip
                v-if="limit"
                :content="`Select up to ${limit} Employees`"
                placement="top">
                <i
                    class="fa fa-info-circle ml-2"
                    aria-hidden="true">
                </i>
            </el-tooltip>
        </label>
        <el-select
            v-model="model"
            @change="handleChange"
            filterable
            clearable
            :multiple-limit="limit ? limit : null"
            :multiple="isMultiple"
            :collapse-tags="isMultiple"
            :placeholder="`Please select ${isMultiple ? 'employees' : 'an employee'}`"
            class="w-100">
            <el-option
                v-if="isMultiple"
                label="Select All"
                :value="null">
            </el-option>
            <el-option
                v-for="employee in employees"
                :key="employee.id"
                :value="employee[property]"
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
            limit: {
                type: Number,
                default: null,
            },
            showLabel: {
                type: Boolean,
                default: true
            },
            value: {},
            property: {
                default: 'id'
            }
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
                if (this.isMultiple) {
                    if (this.model[this.model.length - 1] === null) {
                        this.model = [null]
                    } else {
                        let index = this.model.findIndex(em => em === null)

                        if (index > -1) {
                            this.model.splice(index, 1)
                        }
                    }
                }

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
