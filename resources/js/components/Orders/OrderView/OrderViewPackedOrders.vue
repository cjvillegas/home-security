<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Order Packing"
        :before-close="closeDialog"
        width="50%"
        top="10vh">
        <div v-if="hasScanners">
            {{ totalScanned | numFormat }} of {{ totalBlind | numFormat }} Packed Blinds
        </div>
        {{ privacy }}
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
                        <span>{{ scope.row[column.key] | fixDateTimeByFormat }}</span>
                    </template>
                    <template v-else>
                        <span>{{ scope.row[column.key] }}</span>
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
    import { mapGetters } from 'vuex'

    export default {
        name: "OrderViewPackedOrders",

        mixins: [dialog],

        props: {
            scanners: {
                required: true
            },

            order: {
                required: true
            }
        },

        data() {
            return {
                packedOrders: []
            }
        },

        computed: {
            ...mapGetters(['privacy']),

            columns() {
                return [
                    {label: 'Serial ID', key: 'serial_id', showTooltip: true, sortable: true},
                    {label: 'Date', key: 'scannedtime', showTooltip: true, sortable: true},
                    {label: 'Employee', key: this.privacy ? 'barcode' : 'employee_name', showTooltip: true, sortable: true},
                    {label: 'Operation', key: 'process_name', showTooltip: true, sortable: true},
                ]
            },

            hasScanners() {
                return this.scanners && this.scanners.length
            },

            totalBlind() {
                return this.order ? this.order.total_blinds : 0
            },

            totalScanned() {
                if (!this.scanners || !this.scanners.length) {
                    return 0
                }

                return this.scanners.reduce((acc, cur) => {
                    if (!acc.some(a => a === cur.serial_id)) {
                        acc = [...acc, ...[cur.serial_id]]
                    }

                    return acc
                }, []).length
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
