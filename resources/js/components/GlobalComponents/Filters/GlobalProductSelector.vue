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
            :placeholder="`Please select ${isMultiple ? 'products' : 'a product'}`"
            class="w-100">
            <el-option
                v-for="(product, productKey) in products"
                :key="productKey"
                :value="product.blind_type"
                :label="product.blind_type | ucWords">
            </el-option>
        </el-select>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "GlobalProductSelector",

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
            ...mapGetters(['products']),

            title() {
                return this.isMultiple ? 'Products' : 'Product'
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
