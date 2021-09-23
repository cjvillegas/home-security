<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Order Timeline"
        @close="closeForm"
        append-to-body
        width="40%">
        <div class="container mt-5 mb-5"
            v-loading="loading">
            <div class="row">
                <div class="col-md-12">
                    <el-timeline>
                        <el-timeline-item
                            v-for="process in processList"
                            :key="process.id"
                            :type="process.type">
                            <el-card>
                                <h3> {{ process.label }} </h3>
                                <div
                                    class="overflow-auto"
                                    v-for="(scanner, scannerKey) in filterScannersPerProcess(process.barcode)"
                                    :key="scannerKey">
                                    <div>
                                        {{ (scanner.fullname ? scanner.fullname : '') | valueForEmptyText }}
                                        <span class="float-right">{{ scanner.scannedtime | fixDateByFormat('MMM DD, YYYY hh:mm a') }}</span>
                                    </div>
                                </div>
                            </el-card>
                        </el-timeline-item>
                    </el-timeline>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
    import {dialog} from "../../mixins/dialog";
    export default {
        name: "OrderTimeline",
        mixins: [dialog],
        props: {
            processList: {
                required: true,
                type: Array
            },
        },
        data() {
            return {
                loading: false
            }
        },

        mounted() {
            this.getScannersData(this.order_no)
        },

        computed: {
            ...mapGetters('orders', ['scanners', 'order_no'])
        },

        methods: {
            ...mapActions('orders', ['getScannersData']),
            // getScannersInfo(barcode, order_no) {
            //     let apiUrl = `/admin/scanners/get-scanners-by-barcode`

            //     axios.post(apiUrl, {'processid' : barcode, 'order_no': order_no})
            //     .then((response) => {
            //         console.log(response.data)
            //         this.scanners = response.data.scanners
            //     })
            //     .catch((err) => {
            //         console.log(err)
            //     })
            // },

            filterScannersPerProcess(id) {
                let filteredScanners = this.scanners.filter(scanner => {
                    return scanner.processid == id
                })

                return filteredScanners
            },

            closeForm() {
                this.closeModal()
            }
        },
    }
</script>
