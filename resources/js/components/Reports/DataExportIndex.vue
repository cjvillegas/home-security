<template>
    <el-card class="box-card" v-loading="loading">
        <el-form
            :model="dataExportForm"
            label-position="top">
            <el-form-item
                label="Select Report Range"
                prop="start">
                <el-date-picker
                    v-model="dataExportForm.daterange"
                    type="daterange"
                    align="left"
                    class="w-100"
                    unlink-panels
                    value-format="yyyy-MM-dd"
                    range-separator="~"
                    start-placeholder="Select Start of Report"
                    end-placeholder="End Start of Report"
                    :picker-options="pickerOptions">
                </el-date-picker>
            </el-form-item>
        </el-form>

        <el-button
            :disabled="isButtonDisabled"
            type="primary"
            @click="exportData">
            <i class="fas fa-file-export"></i> Export Data
        </el-button>
    </el-card>
</template>

<script>
    import moment from "moment"
    import fileExporter from '../../mixins/fileExporter'

export default {
    name: "DataExportIndex",
    mixins: [fileExporter],
    data() {
        return {
            loading: false,
            dataExportForm: {
                daterange: []
            },
            pickerOptions: {
                shortcuts: [
                    {
                        text: 'This week',
                        onClick(picker) {
                            picker.$emit('pick', [moment().startOf('week').format('YYYY-MM-DD'), moment().endOf('week').format('YYYY-MM-DD')]);
                        }
                    },
                    {
                        text: 'This month',
                        onClick(picker) {
                            picker.$emit('pick', [moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD')]);
                        }
                    },
                    {
                        text: 'This year',
                        onClick(picker) {
                            const start = new Date(new Date().getFullYear(), 0);
                            picker.$emit('pick', [moment().startOf('year').format('YYYY-MM-DD'), moment().format('YYYY-MM-DD')]);
                        }
                    },
                    {
                        text: 'Last week',
                        onClick(picker) {
                            picker.$emit('pick', [moment().subtract(1, 'week').startOf('week').format('YYYY-MM-DD'), moment().subtract(1,'week').endOf('week').format('YYYY-MM-DD')]);
                        }
                    },
                    {
                        text: 'Last month',
                        onClick(picker) {
                            picker.$emit('pick', [moment().subtract(1,'month').startOf('month').format('YYYY-MM-DD'), moment().subtract(1,'month').endOf('month').format('YYYY-MM-DD')]);
                        }
                    },
                    {
                        text: 'Last year',
                        onClick(picker) {
                            picker.$emit('pick', [moment().subtract(1,'year').startOf('year').format('YYYY-MM-DD'), moment().subtract(1,'year').endOf('year').format('YYYY-MM-DD')]);
                        }
                    }
                ]
            },
        }
    },
    methods: {
        exportData() {
            this.loading = true

            let start = this.dataExportForm.daterange[0]
            let end = this.dataExportForm.daterange[1]

            // sanity checks if both SOD and EOD has value
            if (!start || !end) {
                this.$notify.error({
                    title: 'Invalid Input',
                    message: 'Please specify the report dates.'
                });
            }

            this.$API.Exports.exportRawData(start, end)
            .then(res => {
                this.exporter('xlsx', `Raw Scanners Data (${start} - ${end})`, res.data)
            })
            .catch(err => {
                console.log(err)
            })
            .finally(_ => {
                this.loading = false
            })
        }
    },
    computed: {
        isButtonDisabled() {
            return !this.dataExportForm.daterange || this.dataExportForm.daterange.length !== 2
        }
    }
}
</script>

<style>
    .el-range__close-icon {
        margin-left: auto;
    }
</style>
