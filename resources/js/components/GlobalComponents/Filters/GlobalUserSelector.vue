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
            :placeholder="`Please select ${isMultiple ? 'users' : 'a user'}`"
            class="w-100">
            <el-option
                v-for="user in users"
                :key="user.id"
                :value="user.id"
                :label="user.name | ucWords">
            </el-option>
        </el-select>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "GlobalUserFilterSelector",

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
            ...mapGetters(['users']),

            title() {
                return this.isMultiple ? 'Users' : 'User'
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
