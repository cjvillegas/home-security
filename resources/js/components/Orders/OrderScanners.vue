<template>
    <div>
        <el-card
            class="box-card mt-3"
            v-loading="loading">
            <h4>Scanners</h4>
            <el-input
                placeholder="Press enter to search order scanners..."
                clearable
                style="width: 250px"
                v-model="search"
                @keyup.enter.native="applySearch">
            </el-input>

            <el-table
                fit
                :data="filteredScanners"
                class="mt-3">
                <el-table-column
                    v-for="column in columns"
                    :key="column.key"
                    :prop="column.key"
                    :label="column.label"
                    :show-overflow-tooltip="column.show_overflow_tooltip"
                    sortable>
                    <template slot-scope="scope">
                        <span>{{ scope.row[column.key] }}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    v-if="user && user.permissions && user.permissions.qc_tag_show"
                    label="QC Tagged"
                    prop="qc_tagged"
                    show-overflow-tooltip>
                    <template slot-scope="scope">
                        <el-tag
                            v-if="scope.row.qc_fault"
                            type="danger"
                            size="mini">
                            <i class="fas fa-tag"></i> QC Tagged
                        </el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    label="Actions"
                    width="200">
                    <template slot-scope="scope">
                        <el-tooltip
                            v-if="scope.row.employee_id"
                            class="item"
                            effect="dark"
                            content="QC Tag"
                            :open-delay="500"
                            placement="top">
                            <el-button
                                v-if="showQcTagButton(scope.row)"
                                size="mini"
                                type="danger"
                                @click="handleClickQcTagging(scope.row)">
                                {{ scope.row.qc_fault ? 'Update QC Tag' : 'QC Tag' }}
                            </el-button>
                        </el-tooltip>

                        <el-tooltip
                            v-else
                            class="item"
                            effect="dark"
                            content="Cannot QC tag this row. Employee data is missing."
                            :open-delay="500"
                            placement="top">
                            <i class="fas fa-info-circle"></i>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </el-table>

            <div class="text-right mt-3">
                <el-pagination
                    background
                    layout="total, sizes, prev, pager, next"
                    :total="filters.total"
                    :page-size="filters.size"
                    :page-sizes="[10, 25, 50, 100]"
                    :current-page="filters.page"
                    @size-change="handleSize"
                    @current-change="handlePage">
                </el-pagination>
            </div>
        </el-card>

        <qc-tag-form
            :qcCodes="qcCodes"
            :model="qcTag"
            :user="user"
            :scanner="scanner"
            :visible.sync="showQcTagForm"
            @close="closeQcTagForm">
        </qc-tag-form>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    import pagination from "../../mixins/pagination";
import { mapGetters } from 'vuex';

    export default {
        name: "OrderScanners",
        mixins: [
            pagination
        ],
        props: {
            user: {},
            scannersList: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                showQcTagForm: false,
                loading: false,
                scanners: [],
                qcCodes: [],
                filters: {
                    searchString: null
                },
                search: null,
                scanner: null,
                qcTag: null
            }
        },

        computed: {
            ...mapGetters(['privacy']),
            columns() {
                return  [
                    {label: 'ID', key: 'id', show_overflow_tooltip: true},
                    {label: this.privacy ? 'Barcode' : 'Employee', key: this.privacy ? 'barcode' : 'employee_name' , show_overflow_tooltip: true},
                    {label: 'Operation', key: 'process_name', show_overflow_tooltip: true},
                    {label: 'Blind ID', key: 'blind_id', show_overflow_tooltip: true},
                    {label: 'Scanned At', key: 'scanned_at', show_overflow_tooltip: true},
                    {label: 'Shift', key: 'shift', show_overflow_tooltip: true},
                    {label: 'Team', key: 'team', show_overflow_tooltip: true}
                ]
            },

            filteredScanners() {
                let scanners = cloneDeep(this.scanners)

                let page = this.filters.page
                let offset = (page - 1) * this.filters.size
                let size = this.filters.size * page

                // checks if the search query is present
                if (this.search) {
                    let query = this.search.toLowerCase()

                    // do the local searching
                    scanners = scanners.filter(order => {
                        let blindId = order.blind_id ? order.blind_id.toString().toLowerCase() : ''
                        let customer = order.customer ? order.customer.toString().toLowerCase() : ''
                        let quantity = order.quantity ? order.quantity.toString().toLowerCase() : ''
                        let blindType = order.blind_type ? order.blind_type.toString().toLowerCase() : ''
                        let blindStatus = order.blind_status ? order.blind_status.toString().toLowerCase() : ''
                        let serialId = order.serial_id ? order.serial_id.toString().toLowerCase() : ''

                        return blindId.indexOf(query) > -1 || customer.indexOf(query) > -1 || quantity.indexOf(query) > -1 || blindType.indexOf(query) > -1
                            || blindStatus.indexOf(query) > -1 || serialId.indexOf(query) > -1
                    })
                }
                this.filters.total = scanners.length
                // do local pagination
                // this retrieve scanners in between the current offset and the limit
                scanners = scanners.filter((item, index) => (index + 1) > offset && (index + 1) <= size)

                return scanners
            }
        },

        created() {
            this.getQualityControlCodes()

            this.$EventBus.listen('QC_TAG_CREATE', tag => {
                let index = this.scanners.findIndex(s => s.id === tag.scanner_id)

                if (index > -1) {
                    let scanner = this.scanners[index]
                    scanner.qc_fault = cloneDeep(tag)
                    this.scanners.splice(index, 1, scanner)
                }
            })

            this.$EventBus.listen('QC_TAG_DELETE', scanner => {
                let index = this.scanners.findIndex(s => s.id === scanner.id)

                if (index > -1) {
                    let scanner = this.scanners[index]
                    scanner.qc_fault = null
                    this.scanners.splice(index, 1, scanner)
                }
            })

            this.$EventBus.listen('QC_TAG_UPDATE', tag => {
                let index = this.scanners.findIndex(s => s.id === tag.scanner_id)

                if (index > -1) {
                    let scanner = this.scanners[index]
                    scanner.qc_fault = cloneDeep(tag)
                    this.scanners.splice(index, 1, scanner)
                }
            })
        },
        methods: {
            getQualityControlCodes() {
                axios.post(`/admin/quality-control/list`)
                    .then(res => {
                        this.qcCodes = res.data.qualityControls
                    })
            },
            formatScanners() {
                let scanners = cloneDeep(this.scanners)

                this.scanners = scanners.map(scanner => {
                    scanner.blind_id = scanner.blindid
                    scanner.scanned_at = this.$DateService.formatDateTime(scanner.scannedtime, 'YYYY-MM-DD HH:mm:ss', 'MMM DD, YYYY HH:mm')
                    scanner.shift = scanner.employee && scanner.employee.shift ? scanner.employee.shift.name : ''
                    scanner.team = scanner.employee && scanner.employee.team ? scanner.employee.team.name : ''

                    return scanner
                })
            },
            handleClickQcTagging(scanner) {
                if (!scanner.employee_id) {
                    this.$notify({
                        title: 'Scanner',
                        message: `Cannot QC tag this process, employee data is missing.`,
                        type: 'error'
                    })

                    return
                }

                this.qcTag = scanner.qc_fault ? cloneDeep(scanner.qc_fault) : null
                this.scanner = cloneDeep(scanner)
                this.showQcTagForm = true
            },
            closeQcTagForm() {
                this.scanner = null
                this.showQcTagForm = false
            },
            showQcTagButton(scanner) {
                if (scanner.qc_fault && this.user.permissions.qc_tag_edit) {
                    return true
                }

                return !!(!scanner.qc_fault && this.user.permissions.qc_tag_create);
            }
        },
        watch: {
            scannersList: {
                handler() {
                    this.scanners = cloneDeep(this.scannersList)

                    this.formatScanners()
                },
                deep: true,
                immediate: true
            }
        }
    }
</script>

<style scoped>

</style>
