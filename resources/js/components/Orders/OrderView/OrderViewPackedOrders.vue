<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Packed Orders"
        :before-close="closeDialog"
        width="50%"
        top="10vh">
        <el-table
            v-if="hasScanners"
            fit
            :data="packedOrders"
            class="mt-3">
            <el-table-column
                v-for="column in columns"
                :key="column.key"
                :prop="column.key"
                :label="column.label"
                :show-overflow-tooltip="column.show_overflow_tooltip"
                sortable>
                <template slot-scope="scope">
                    <template v-if="['employee_name', 'process_name'].includes(column.key)">
                        {{ scope.row[column.key] | ucWords }}
                    </template>
                    <template v-else-if="column.key === 'scannedtime'">
                        <span>{{ scope.row[column.key] | fixDateByFormat }}</span>
                    </template>
                    <template v-else-if="column.key === 'serial_id'">
                        <span>{{ scope.row[column.key] | numFormat }}</span>
                    </template>
                </template>
            </el-table-column>
        </el-table>

        <el-empty
            v-else
            description="No Packed Orders Yet. Wait for the next sync of the data from the BlindData database and recheck again.">
        </el-empty>
    </el-dialog>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import { dialog } from '../../../mixins/dialog'

    export default {
        name: "OrderViewPackedOrders",

        mixins: [dialog],

        props: {
            scanners: {
                required: true
            }
        },

        data() {
            let columns = [
                {label: 'Serial ID', key: 'serial_id', showTooltip: true, sortable: true},
                {label: 'Date', key: 'scannedtime', showTooltip: true, sortable: true},
                {label: 'Employee', key: 'employee_name', showTooltip: true, sortable: true},
                {label: 'Operation', key: 'process_name', showTooltip: true, sortable: true},
            ]

            return {
                columns: columns,
                packedOrders: []
            }
        },

        computed: {
            hasScanners() {
                return this.scanners && this.scanners.length
            }
        },

        methods: {
            closeDialog() {
                this.closeModal()
            }
        },

        watch: {
            scanners: {
                handler() {
                    this.packedOrders = cloneDeep(this.scanners)
                },
                immediate: true,
                deep: true
            }
        }
    }
</script>
