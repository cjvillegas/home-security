<template>
    <div v-loading="loading">
        <el-card class="box-card">
            <div class="d-flex align-items-center">
                <el-button
                    @click="backToList">
                    <i class="fas fa-arrow-left"></i> Back to List
                </el-button>

                <h4 class="mb-0 ml-3">
                    <span v-if="notification && notification.data.message">
                        {{ notification.data.message | ucWords }}
                    </span>
                    <span v-else>Notification</span>
                </h4>
            </div>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div v-if="notification">
                <el-descriptions
                    class="margin-top"
                    :column="1"
                    size="medium"
                    direction="vertical"
                    border>
                    <template slot="extra">
                        <el-button
                            @click="deleteNotification"
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
                        {{ notification.id }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fab fa-periscope"></i>
                            Type
                        </template>
                        <el-tag
                            :type="getType(notification.data.type)"
                            effect="dark">
                            {{ notification.data.type | ucWords }}
                        </el-tag>
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-location-arrow"></i>
                            From
                        </template>
                        {{ notification.data.from | ucWords }}
                        <span v-if="notification.data.from === 'cron'">
                            : {{ notification.data.cron | ucWords }} @ {{ notification.data.date | fixDateTimeByFormat('MMM DD, YYYY HH:mm') }}
                        </span>
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-clock"></i>
                            Recorded At
                        </template>
                        {{ notification.created_at | fixDateTimeByFormat }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-envelope-open"></i>
                            Error
                        </template>
                        <pre>
                            <code>
                                {{ notification.data.error_message}}
                            </code>
                        </pre>
                    </el-descriptions-item>
                </el-descriptions>
            </div>

            <el-empty
                v-else
                description="No Notification Found. The notification might be deleted or not existing in your company.">
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
        name: "NotificationView",
        props: {
            id: {
                required: true
            }
        },
        data() {
            return {
                loading: false,
                notification: null
            }
        },
        created() {
            if (this.id) {
                this.getNotification()
            }
        },
        methods: {
            getNotification() {
                this.loading = true

                this.$API.Notification.show(this.id)
                    .then(res => {
                        this.notification = res.data
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            deleteNotification() {
                this.$confirm('Are you sure you want to delete this notification?', 'Confirm', {
                    confirmButtonText: "Yes, I'm Sure",
                    cancelButtonText: 'No, Not Sure',
                    type: 'error',
                    confirmButtonClass: 'el-button--danger'
                })
                    .then(_ => {
                        this.loading = true

                        this.$API.Notification.delete(this.notification.id)
                            .then(res => {
                                if (res.data) {
                                    this.$notify({
                                        title: 'Success',
                                        message: 'Notification successfully deleted.',
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
            getType(type) {
                if (type === 'error') {
                    return 'danger'
                }

                return 'info'
            },
            backToList() {
                this.$router.push({name: 'Notification List'})
            }
        },
        beforeRouteEnter(to, from, next) {
            if (to.params && !to.params.id) {
                next({replace: true, name: 'Notification List'})
            }

            next()
        },
    }
</script>
