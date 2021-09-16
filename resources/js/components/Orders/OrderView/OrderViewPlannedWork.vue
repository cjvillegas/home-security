<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Planned Work"
        :before-close="closeDialog"
        width="50%"
        top="10vh">
        <el-table
            v-if="hasPlannedWork"
            v-loading="loading"
            fit
            :data="plannedWorks"
            class="mt-3">
            <el-table-column
                v-for="column in columns"
                :key="column.key"
                :prop="column.key"
                :label="column.label"
                :show-overflow-tooltip="column.showTooltip"
                sortable>
                <template slot-scope="scope">
                    <template v-if="['scheduled_date', 'work_date'].includes(column.key)">
                        {{ scope.row[column.key] | fixDateByFormat }}
                    </template>
                    <template v-else-if="column.key === 'folder_name'">
                        <span>{{ scope.row[column.key] | ucWords }}</span>
                    </template>
                    <template v-else>
                        <span>{{ scope.row[column.key] }}</span>
                    </template>
                </template>
            </el-table-column>
        </el-table>

        <el-empty
            v-else
            description="No Planned Work Yet. Wait for the next sync of the data from the BlindData database and recheck again.">
        </el-empty>
    </el-dialog>
</template>

<script>
    import { dialog } from '../../../mixins/dialog'

    export default {
        name: "OrderViewPlannedWork",

        mixins: [dialog],

        props: {
            order: {
                required: true
            },
            updateParent: {
                type: Boolean,
                default: false
            }
        },

        data() {
            let columns = [
                {label: 'Serial ID', key: 'serial_id', showTooltip: true, sortable: true},
                {label: 'Shift/Team', key: 'folder_name', showTooltip: true, sortable: true},
                {label: 'Scheduled Date', key: 'scheduled_date', showTooltip: true, sortable: true},
                {label: 'Working Date', key: 'work_date', showTooltip: true, sortable: true},
            ]
            return {
                loading: false,
                columns: columns,
                plannedWorks: []
            }
        },

        computed: {
            hasPlannedWork() {
                return this.plannedWorks && this.plannedWorks.length
            }
        },

        methods: {
            getPlannedWork() {
                this.loading = true

                this.$API.Orders.getOrderPlannedWork(this.order.order_no)
                .then(res => {
                    this.plannedWorks = res.data
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            closeDialog() {
                this.closeModal()
            }
        },

        watch: {
            order: {
                handler() {
                    if (this.order && this.order.order_no) {
                        this.getPlannedWork()
                    }
                },
                immediate: true,
                deep: true
            }
        }
    }
</script>
