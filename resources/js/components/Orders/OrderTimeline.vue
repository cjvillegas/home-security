<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Order Timeline"
        @close="closeForm"
        append-to-body
        width="40%">
        <div class="container mt-5 mb-5">
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
                                    v-for="(scanner, scannerKey) in process.scanners"
                                    :key="scannerKey">
                                    <div
                                        v-if="scanner.processid == process.barcode">
                                        {{ (scanner.employee ? scanner.employee.fullname : '') | valueForEmptyText }}
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
    import {dialog} from "../../mixins/dialog";
    export default {
        name: "OrderTimeline",
        mixins: [dialog],
        props: {
            processList: {
                required: true,
                type: Array
            }
        },
        data() {
            return {

            }
        },

        methods: {
            closeForm() {
                this.closeModal()
            }
        }
    }
</script>
