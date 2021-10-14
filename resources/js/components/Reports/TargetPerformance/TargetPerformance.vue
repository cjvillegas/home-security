<template>
    <div>
        <global-page-header title="Target Performance"></global-page-header>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div class="ml-auto">
                    <el-button
                        @click="openFilter">
                        <i class="fas fa-filter"></i> Filters
                    </el-button>
                </div>
            </div>

            <div v-if="!hasPerformancesData">
                <el-empty
                    description="No Records Found. Please select filters and click apply to see the data you want to get displayed.">
                </el-empty>
            </div>
        </el-card>

        <target-performance-filter
            :visible.sync="showFilterDialog"
            :filters.sync="filters"
            @close="showFilterDialog = false">
        </target-performance-filter>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    export default {
        name: "TargetPerformance",

        data() {
            return {
                showFilterDialog: false,
                filters: {
                    employees: [],
                    dateRange: null,
                    isNewJoiner: false,
                }
            }
        },

        computed: {
            ...mapGetters('targetperformance', ['performances', 'loading']),

            hasPerformancesData() {
                return this.performances > 0
            }
        },

        methods: {
            openFilter() {
                this.showFilterDialog = true
            }
        }
    }
</script>
