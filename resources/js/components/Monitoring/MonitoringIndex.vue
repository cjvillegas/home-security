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
                    :class="`block-cards ${setStatus(monitor)}`">
                    <div class="block-name">
                        <el-tooltip
                            v-if="Number(monitor.status) === STATUS.STATUS_BURNING"
                            class="item"
                            effect="dark"
                            content="This house might be under fire."
                            placement="top">
                            <i class="fas fa-burn"></i>
                        </el-tooltip>

                        <el-tooltip
                            v-else-if="Number(monitor.status) === STATUS.STATUS_BURGLAR"
                            class="item"
                            effect="dark"
                            content="This house might be under burglary."
                            placement="top">
                            <i class="fas fa-theater-masks"></i>
                        </el-tooltip>

                        <el-tooltip
                            v-else
                            class="item"
                            effect="dark"
                            content="Safe and protected."
                            placement="top">
                            <i class="fas fa-user-shield"></i>
                        </el-tooltip>



                        {{ monitor.name }}
                    </div>

                    <div class="block-ip-address">
                        {{ monitor.ip_address }}
                    </div>

                    <div class="action-buttons">
                        <el-popconfirm
                            v-if="Number(monitor.status) !== STATUS.STATUS_NORMAL"
                            @confirm="revertStatus(monitor)"
                            confirm-button-text='Yes'
                            cancel-button-text='Not sure'
                            icon="el-icon-info"
                            icon-color="green"
                            title="Do you wish to change status to normal?">
                            <el-button
                                slot="reference"
                                type="text"
                                class="text-success">
                                <i class="fas fa-check-circle"></i>
                            </el-button>
                        </el-popconfirm>

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
    import * as STATUS from '../../constants/status'

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
                selectedModel: null,
                STATUS
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
                this.doUpdate(data)
            })

            window.GlobalEventBus.listen('GLOBAL_MONITORING_DELETED', data => {
                this.doDeletion(data)
            })

            window.GlobalEventBus.listen('GLOBAL_MONITORING_STATUS_CHANGE', data => {
                this.doUpdate(data)
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
                            this.doDeletion(item)

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

            doDeletion(item) {
                const index = this.monitorings.findIndex(m => m.id === item.id)
                if (index > -1) {
                    this.monitorings.splice(index, 1)
                }
            },

            revertStatus(item) {
                this.loading = true

                const postData = {
                    status: STATUS.STATUS_NORMAL
                }

                this.$API.Monitoring.changeStatus(item.id, postData)
                    .then(res => {
                        this.doUpdate(res.data)
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

            doUpdate(item) {
                const index = this.monitorings.findIndex(m => m.id === item.id)
                if (index > -1) {
                    this.monitorings.splice(index, 1, item)
                }
            },

            addNewMonitoring() {
                this.showForm = true
            },

            formClosed() {
                this.selectedModel = null
                this.showForm = false
            },

            setStatus(item) {
                if (STATUS.STATUS_BURNING === Number(item.status)) {
                    return 'status-burning'
                }

                if (STATUS.STATUS_BURGLAR === Number(item.status)) {
                    return 'status-burglar'
                }

                return ''
            }
        }
    }
</script>

<style lang="scss">
    .status-burning {
        background-color: #F56C6C !important;
        color: #FFFF !important;

        .action-buttons {
            color: #FFFFFF !important;
        }
    }

    .status-burglar {
        background-color: #E6A23C !important;
        color: #FFFF !important;

        .action-buttons {
            color: #FFFFFF !important;
        }
    }

    .block-cards {
        width: 200px;
        height: 85px;
        margin-left: 10px;
        margin-right: 10px;
        margin-top: 10px;
        position: relative;
        border-color: #67C23A;

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
