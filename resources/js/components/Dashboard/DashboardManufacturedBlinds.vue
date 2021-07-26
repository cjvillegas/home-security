<template>
    <div v-loading="loading">
        <div class="text-right">
            <el-button
                @click="getManufacturedBlindsAnalytics"
                class="mb-3"
                size="mini">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </el-button>
        </div>

        <el-card class="box-card">
            <div slot="header" class="clearfix d-flex">
                <h4>Today's Manufactured Blinds</h4>
            </div>

            <div class="row">
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge
                        title="Shift 1"
                        :count="total.today_shift_1">
                    </global-gauge>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge
                        title="Shift 2"
                        :count="total.today_shift_2">
                    </global-gauge>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge
                        title="Shift 3"
                        :count="total.today_shift_3">
                    </global-gauge>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge
                        title="Total"
                        :count="total.today_shift_total">
                    </global-gauge>
                </div>
            </div>
        </el-card>

        <el-card class="box-card mt-5">
            <div slot="header" class="clearfix d-flex">
                <h4>Yesterday's Manufactured Blinds</h4>
            </div>

            <div class="row">
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge
                        title="Shift 1"
                        :count="total.yesterday_shift_1">
                    </global-gauge>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge
                        title="Shift 2"
                        :count="total.yesterday_shift_2">
                    </global-gauge>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge
                        title="Shift 3"
                        :count="total.yesterday_shift_3">
                    </global-gauge>
                </div>
                <div class="mb-3 col-sm-12 col-md-6 col-lg-3 d-flex justify-content-center">
                    <global-gauge
                        title="Total"
                        :count="total.yesterday_shift_total">
                    </global-gauge>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
    export default {
    name: "ManufacturedBlinds",
    data() {
        return {
            loading: false,
            total: {
                today_shift_1: 0,
                today_shift_2: 0,
                today_shift_3: 0,
                today_shift_total: 0,
                yesterday_shift_1: 0,
                yesterday_shift_2: 0,
                yesterday_shift_3: 0,
                yesterday_shift_total: 0,
            },
        }
    },
    created() {
        this.getManufacturedBlindsAnalytics()
    },
    methods: {
        getManufacturedBlindsAnalytics() {
            this.loading = true

            this.$API.Reports.manufacturedBlindsAnalytics()
                .then(res => {
                    this.total = res.data
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
        }
    }
}
</script>
