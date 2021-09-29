<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Manufactured Blinds</h4>
        </el-card>

        <el-card
            v-loading="loading"
            class="box-card mt-3">
            <div class="d-flex">
                <div class="ml-auto">
                    <global-filter-box>
                        <global-date-range-selector
                            :value.sync="filters.dateRange">
                        </global-date-range-selector>

                        <el-button
                            @click="getBlinds(filters)"
                            :disabled="disableApplyFilterButton"
                            type="primary"
                            class="w-100 mt-4">
                            Apply Filter
                        </el-button>
                    </global-filter-box>
                    <el-button
                        @click="exportManufacturedBlinds"
                        :disabled="!canExportData"
                        type="success">
                        <i class="fas fa-file-export"></i> Export
                    </el-button>
                </div>
            </div>

            <el-table>
                <el-table-column
                    label="Date">
                </el-table-column>

                <el-table-column
                    label="Shift">
                </el-table-column>

                <el-table-column
                    label="Manufactured Blinds">
                </el-table-column>

                <el-table-column
                    label="Invoiced Blinds">
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
    export default {
        data() {
            return {
                filters: {
                    dateRange: null
                }
            }
        },

        computed: {
            ...mapGetters('manufacturedblinds', ['blinds', 'loading']),

            canExportData() {
                return false
            },
            disableApplyFilterButton() {
                return false
            }
        },

        methods: {
            ...mapActions('manufacturedblinds', ['getBlinds', 'exportManufacturedBlinds']),
        }
    }
</script>
