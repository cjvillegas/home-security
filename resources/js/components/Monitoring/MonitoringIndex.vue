<template>
    <div v-loading="loading">
        <el-card class="box-card">
            <h4 class="mb-0">Monitoring</h4>
        </el-card>

        <el-card class="box-card mt-3">
            <div class="d-flex">
                <el-button
                    type="primary"
                    class="ml-auto"
                    @click="addNewMonitoring">
                    <i class="fas fa-plus"></i>
                    Add New Block
                </el-button>
            </div>

            <div class="mt-3 d-flex flex-wrap">
                <el-card
                    v-for="monitor in monitorings"
                    :key="monitor.id"
                    shadow="always"
                    class="block-cards">
                    <div class="block-name">
                        {{ monitor.name }}
                    </div>

                    <div class="block-ip-address">
                        {{ monitor.ip_address }}
                    </div>

                    <div class="action-buttons">
                        <el-button
                            type="text"
                            @click="updateBlock(monitor)">
                            <i class="fas fa-pen-square"></i>
                        </el-button>

                        <el-popconfirm
                            @confirm="deleteBlock(monitor)"
                            confirm-button-text='OK'
                            cancel-button-text='No, Thanks'
                            icon="el-icon-info"
                            icon-color="red"
                            title="Are you sure to delete this Block?">
                            <el-button
                                slot="reference"
                                type="text"
                                class="text-danger ml-1">
                                <i class="fas fa-trash-alt"></i>
                            </el-button>
                        </el-popconfirm>
                    </div>
                </el-card>
            </div>
        </el-card>

        <monitoring-form
            :visible.sync="showForm"
            :model="selectedModel"
            @close="formClosed">
        </monitoring-form>
    </div>
</template>

<script>
    import MonitoringForm from "./MonitoringForm"

    export default {
        name: "MonitoringIndex",

        components: {
            MonitoringForm
        },

        data() {
            return {
                showForm: false,
                loading: false,
                monitorings: [],
                selectedModel: null
            }
        },

        created() {
            this.fetchAll()

            this.$EventBus.listen('MONITORING_SAVED', data => {
                if (data) {
                    this.monitorings.push(data)
                }
            })

            this.$EventBus.listen('MONITORING_UPDATED', data => {
                const index = this.monitorings.findIndex(m => m.id === data.id)
                if (index > -1) {
                    this.monitorings.splice(index, 1, data)
                }
            })
        },

        methods: {
            fetchAll() {
                this.loading = true

                this.$API.Monitoring.list()
                    .then(res => {
                        this.monitorings = res.data
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            updateBlock(item) {
                this.selectedModel = item
                this.showForm = true
            },

            deleteBlock(item) {
                this.loading = true

                this.$API.Monitoring.delete(item.id)
                    .then(res => {
                        if (res.data) {
                            const index = this.monitorings.findIndex(m => m.id === item.id)
                            if (index > -1) {
                                this.monitorings.splice(index, 1)
                            }

                            this.$notify({
                                type: 'success',
                                title: 'Monitoring',
                                message: 'Item deleted.',
                                duration: 5000
                            })
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            addNewMonitoring() {
                this.showForm = true
            },

            formClosed() {
                this.selectedModel = null
                this.showForm = false
            }
        }
    }
</script>

<style lang="scss">
    .block-cards {
        width: 200px;
        height: 85px;
        margin-left: 10px;
        margin-right: 10px;
        margin-top: 10px;
        position: relative;

        .block-name {
            font-weight: 700;
        }

        .block-ip-address {
            font-size: 12px;
        }

        .action-buttons {
            position: absolute;
            right: 10px;
            bottom: 0;
        }
    }
</style>
