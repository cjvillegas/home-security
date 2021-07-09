<template>
    <div>
        <h2>Welcome to Style</h2>
        <el-card
            class="box-card"
            v-loading="loading">
            <div slot="header" class="clearfix d-flex">
                <h4>Today's Manufactured Blinds</h4>

                <el-button
                    @click="getManufacturedBlindsAnalytics"
                    class="ml-auto"
                    size="mini">
                    <i class="fas fa-sync-alt"></i>
                </el-button>
            </div>

            <div class="d-flex justify-content-around">
                <global-gauge
                    title="Shift 1"
                    :count="total.today_shift_1">
                </global-gauge>

                <global-gauge
                    title="Shift 2"
                    :count="total.today_shift_2">
                </global-gauge>

                <global-gauge
                    title="Shift 3"
                    :count="total.today_shift_3">
                </global-gauge>

                <global-gauge
                    title="Total"
                    :count="total.today_shift_total">
                </global-gauge>
            </div>
        </el-card>

        <el-card
            class="box-card mt-5"
            v-loading="loading">
            <div slot="header" class="clearfix d-flex">
                <h4>Yesterday's Manufactured Blinds</h4>

                <el-button
                    @click="getManufacturedBlindsAnalytics"
                    class="ml-auto"
                    size="mini">
                    <i class="fas fa-sync-alt"></i>
                </el-button>
            </div>

            <div class="d-flex justify-content-around">
                <global-gauge
                    title="Shift 1"
                    :count="total.yesterday_shift_1">
                </global-gauge>

                <global-gauge
                    title="Shift 2"
                    :count="total.yesterday_shift_2">
                </global-gauge>

                <global-gauge
                    title="Shift 3"
                    :count="total.yesterday_shift_3">
                </global-gauge>

                <global-gauge
                    title="Total"
                    :count="total.yesterday_shift_total">
                </global-gauge>
            </div>
        </el-card>
    </div>
</template>

<script>
export default {
    name: "DashboardIndex",
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
            }
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
