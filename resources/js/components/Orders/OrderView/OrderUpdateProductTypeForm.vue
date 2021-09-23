<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Edit Product Type"
        :before-close="closeDialog"
        width="20%"
        top="10vh">
        <el-form
            :model="form">
            <el-form-item
                label="Process"
                prop="product_type">
                <el-autocomplete
                    v-model="form.product_type"
                    :fetch-suggestions="querySearch"
                    placeholder="Search for Process"
                    value-key="name"
                    @select="selectItem">
                </el-autocomplete>
            </el-form-item>
        </el-form>
        <span
            slot="footer"
            class="dialog-footer">
            <el-button
                type="primary"
                @click="saveChanges">
                Save Changes
            </el-button>
        </span>
    </el-dialog>
</template>

<script>
    import { mapActions, mapMutations } from 'vuex'
    import { dialog } from '../../../mixins/dialog'

    export default {
        name: "OrderUpdateProductTypeForm",

        mixins: [dialog],

        props: {
            order: {
                required: true,
            }
        },

        data() {
            return {
                process_name: this.order.product_type,
                form: {
                    order_id: null,
                    product_type: null,
                },
                filters: {
                    searchString: null
                }
            }
        },

        methods: {
            ...mapActions('process', ['searchProcessesList']),
            ...mapActions('orders', ['updateProductType']),
            ...mapMutations('process', ['setProcesses']),

            querySearch(queryString, cb) {
                console.log(this.form)
                let processes = []

                this.searchProcessesList(this.filters)
                .then((response) => {
                    processes = response.data.processes

                    var results = queryString ? processes.filter(this.createFilter(queryString)) : processes
                    cb(results)
                })
            },

            createFilter(queryString) {
                return (process) => {
                    return (process.name.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                };
            },

            selectItem(item) {
                this.form.processid = item.id
            },

            saveChanges() {
                this.updateProductType(this.form)
                .then((response) => {
                    this.$notify({
                        title: 'Success!',
                        message: response.data.message,
                        type: 'success'
                    });
                })
            },

            closeDialog() {
                this.process_name = null,
                this.closeModal()
            }
        },

        watch: {
            order(value) {
                if (value) {
                    this.form.order_id = this.order.id
                    this.form.product_type = this.order.product_type
                }
            }
        }
    }
</script>
