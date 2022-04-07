<template>
    <el-dialog
        :visible.sync="visible"
        :title="modalTitle"
        width="400px"
        :before-close="closeForm"
        custom-class="sbg-compact-modal">
        <el-form
            v-loading="loading"
            ref="monitoringForm"
            :model="monitoringForm"
            :rules="rules">
            <el-form-item
                label="Name"
                prop="name">
                <el-input
                    v-model="monitoringForm.name"
                    clearable
                    placeholder="B1-L10"
                    class="w-100">
                </el-input>
            </el-form-item>

            <el-form-item
                label="IP Address"
                prop="ip_address">
                <el-input
                    v-model="monitoringForm.ip_address"
                    clearable
                    placeholder="192.168.0.1"
                    class="w-100">
                </el-input>
            </el-form-item>
        </el-form>

        <span
            slot="footer"
            class="dialog-footer">
		    <el-button
                @click="closeForm">
		    	Close
		    </el-button>
		    <el-button
                @click="validate"
                type="primary"
                class="btn-primary">
		    	Save
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
    import {dialog} from "../../mixins/dialog"

    export default {
        name: "MonitoringForm",

        props: {
            model: {
                required: false
            }
        },

        mixins: [dialog],

        data() {
            return {
                loading: false,
                monitoringForm: {
                    id: null,
                    name: null,
                    ip_address: null
                },
                rules: {
                    name: {required: true, message: 'Name is required', trigger: 'blur'}
                }
            }
        },

        computed: {
            hasModel() {
                return !!(this.model && this.model.id)
            },

            modalTitle() {
                return this.hasModel ? 'Update Monitoring' : 'Create A New Monitoring'
            }
        },

        mounted() {
            if (this.hasModel) {
                this.initialize()
            }
        },

        methods: {
            validate() {
                this.$refs.monitoringForm.validate(valid => {
                    if (valid) {
                        if (this.hasModel) {
                            this.updateMonitoring()

                            return
                        }

                        this.saveNewMonitoring()
                    }
                })
            },

            saveNewMonitoring() {
                this.loading = true

                this.$API.Monitoring.store(this.monitoringForm)
                    .then(res => {
                        this.$EventBus.fire('MONITORING_SAVED', res.data)

                        this.$notify({
                            type: 'success',
                            title: 'Monitoring',
                            message: 'A new item is saved.',
                            duration: 5000
                        })

                        this.closeForm()
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            updateMonitoring() {
                this.loading = true

                this.$API.Monitoring.update(this.monitoringForm.id, this.monitoringForm)
                    .then(res => {
                        this.$EventBus.fire('MONITORING_UPDATED', res.data)

                        this.$notify({
                            type: 'success',
                            title: 'Monitoring',
                            message: 'Item updated.',
                            duration: 5000
                        })

                        this.closeForm()
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            initialize() {
                this.monitoringForm.id = this.model.id
                this.monitoringForm.name = this.model.name
                this.monitoringForm.ip_address = this.model.ip_address
            },

            closeForm() {
                this.closeModal()

                this.resetForm()
            },

            resetForm() {
                this.monitoringForm = {
                    id: null,
                    name: null,
                    ip_address: null
                }
            }
        },

        watch: {
            model: {
                handler(value) {
                    if (value) {
                        this.initialize()

                    }
                },
                immediate: true
            }
        }
    }
</script>
