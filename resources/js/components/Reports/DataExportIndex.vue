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
                    @change="datesChange"
                    type="daterange"
                    align="left"
                    class="w-100"
                    value-format="yyyy-MM-dd"
                    range-separator="~"
                    start-placeholder="Select Start of Report"
                    end-placeholder="End Start of Report"
                    :picker-options="pickerOptions">
                </el-date-picker>
            </el-form-item>

            <el-tag
                class="font-weight-bolder font-italic"
                type="info">
                Note*: You cannot select dates more than 31 days. If you have any concerns, please reach to your administrator.
            </el-tag>
        </el-form>

        <el-button
            @click="exportData"
            :disabled="isButtonDisabled"
            type="primary"
            class="mt-3">
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
                    daterange: [],
                    onPick: []
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
                        }
                    ],
                    disabledDate: time => {
                        if (!this.dataExportForm.onPick || !this.dataExportForm.onPick.length) {
                            return false
                        }

                        let momentTime = moment(this.dataExportForm.onPick[0])
                        let momentNow = moment(time)

                        /**
                         * prevent selection of dates that will be more than 31 days
                         * this logic is to prevent stack overflow error in our server when users
                         * want to export loads of data.
                         */
                        return Math.abs(momentTime.diff(momentNow, 'days')) > 31
                    },
                    onPick: ({minDate, maxDate}) => {
                        this.dataExportForm.onPick[0] = minDate
                        this.dataExportForm.onPick[1] = maxDate
                    }
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

                start += ' 00:00:00'
                end += ' 23:59:59'

                this.$API.Exports.exportRawData(start, end)
                .then(res => {
                    if (res.data.success) {
                        this.$notify({
                            title: 'Scanners Raw Data',
                            message: res.data.message,
                            type: 'success'
                        })

                        this.resetForm()
                    }
                })
                .catch(err => {
                    console.log(err)

                    this.$notify.error({
                        title: 'Invalid Input',
                        message: "You can't select dates more than 31 days. If you have any concerns please report this to your administrator."
                    });
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            datesChange() {
                if (this.dataExportForm.daterange && this.dataExportForm.daterange.length) {
                    let [start, end] = this.dataExportForm.daterange
                    if (Math.abs(moment(end).diff(moment(start), 'days')) > 31) {
                        this.dataExportForm.daterange = []

                        this.$notify.error({
                            title: 'Invalid Input',
                            message: "You can't select dates more than 31 days. If you have any concerns please report this to your administrator."
                        });
                    }

                }
            },

            resetForm() {
                this.dataExportForm = {
                    daterange: [],
                    onPick: []
                }
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
