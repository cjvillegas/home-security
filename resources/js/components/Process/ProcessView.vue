<template>
    <div v-loading="loading">
        <el-card class="box-card">
            <div class="d-flex align-items-center">
                <el-button
                    @click="backToList">
                    <i class="fas fa-arrow-left"></i> Back to List
                </el-button>

                <h4 class="mb-0 ml-3">{{ process ? process.name : '' | ucWords }}</h4>
            </div>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div v-if="process">
                <el-descriptions
                    class="margin-top"
                    :column="2"
                    size="medium"
                    direction="vertical"
                    border>
                    <template slot="title">
                        {{ `${process.name} Information` | ucWords }}
                    </template>

                    <template slot="extra">
                        <el-button
                            @click="deleteProcess"
                            type="danger"
                            size="small">
                            Delete
                        </el-button>
                    </template>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-address-card"></i>
                            ID
                        </template>
                        {{ process.id | numFormat }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-barcode"></i>
                            Barcode
                        </template>
                        {{ process.barcode }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-file-signature"></i>
                            Name
                        </template>
                        {{ process.name | ucWords }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-dot-circle"></i>
                            Process Target
                        </template>
                        {{ process.process_target || '--:--' }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="far fa-dot-circle"></i>
                            New Joiner Target
                        </template>
                        {{ process.new_joiner_target || '--:--' }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-calendar-check"></i>
                            Process Manufacturing Time
                        </template>
                        {{ process.process_manufacturing_time | fixDateByFormat }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-calendar-check"></i>
                            Stop Start Button Required
                        </template>
                        <el-tag
                            :type="process.stop_start_button_required ? 'success' : 'info'"
                            effect="dark">
                            {{ process.stop_start_button_required ? 'Yes' : 'No' }}
                        </el-tag>
                    </el-descriptions-item>

                </el-descriptions>
            </div>

            <el-empty
                v-else
                description="No Process Found. The process might be deleted or not existing in your company.">
                <el-button
                    @click="backToList">
                    Back to List
                </el-button>
            </el-empty>
        </el-card>
    </div>
</template>

<script>
    export default {
        name: "ProcessView",

        props: {
            id: {
                required: true
            }
        },

        created() {
            if (this.id) {
                this.getProcess()
            }
        },

        data() {
            return {
                process: null,
                loading: false
            }
        },

        methods: {
            getProcess() {
                this.loading = true

                this.$API.Processes.show(this.id)
                    .then(res => {
                        this.process = res.data
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            deleteProcess() {
                this.$confirm('Are you sure you want to delete this process?', 'Confirm', {
                    confirmButtonText: "Yes, I'm Sure",
                    cancelButtonText: 'No, Not Sure',
                    type: 'error',
                    confirmButtonClass: 'el-button--danger'
                })
                    .then(_ => {
                        this.loading = true

                        this.$API.Processes.delete(this.process.id)
                            .then(res => {
                                if (res.data) {
                                    this.$notify({
                                        title: 'Success',
                                        message: 'Process successfully deleted.',
                                        type: 'success'
                                    })

                                    setTimeout(_ => {
                                        this.backToList()

                                    }, 300)
                                }
                            })
                            .catch(err => {
                                console.log(err)
                                this.loading = false
                            })
                    })
                    .catch(_ => {})

            },

            backToList() {
                this.$router.push({name: 'Process List'})
            }
        },

        beforeRouteEnter(to, from, next) {
            if (to.params && !to.params.id) {
                next({replace: true, name: 'Process List'})
            }

            next()
        },
    }
</script>
