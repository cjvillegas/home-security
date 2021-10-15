<template>
    <el-dialog
        :visible.sync="showDialog"
        title="Target Performance Filter"
        :before-close="closeModal">
        <div class="row">
            <div class="col-md-6">
                <global-employee-selector
                    :value.sync="filters.employees"
                    :is-multiple="true">
                </global-employee-selector>
            </div>
            <div class="col-md-6">
                <global-date-range-selector
                    :value.sync="filters.dateRange">
                </global-date-range-selector>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <div>
                    <label>{{ employeeInformationLabel }}</label>
                    <el-select
                        v-model="filters.isNewJoiner"
                        placeholder="Please Select"
                        class="w-100">
                        <el-option
                            :value="true"
                            label="Yes">
                        </el-option>
                        <el-option
                            :value="false"
                            label="No">
                        </el-option>
                    </el-select>
                </div>
            </div>
            <div class="col-md-6">
                <div>
                    <label>Target</label>
                    <el-select
                        v-model="filters.type"
                        placeholder="Please Select"
                        class="w-100">
                        <el-option
                            value="trade"
                            label="Trade Target">
                        </el-option>
                        <el-option
                            value="internet"
                            label="Internet Target">
                        </el-option>
                    </el-select>
                </div>
            </div>
        </div>
        <span
            slot="footer"
            class="dialog-footer">
		    <el-button
                @click="closeFilter">
		    	Close
		    </el-button>
		    <el-button
                @click="applyFilter"
                type="primary"
                class="btn-primary">
		    	Apply Filter
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
import { mapActions } from 'vuex';
    import { dialog } from "../../../mixins/dialog";
    export default {
         mixins: [dialog],

        props: {
            filters: {
                type: Object,
                required: true
            },
        },

         data() {
             return {
                 confirmationDialog: false
             }
         },

         computed: {
             employeeInformationLabel() {
                 if ( this.filters.employees.length > 1 ) {
                     return "Are these Employees new joiners?"
                 } else {
                     return "Is the Employee new joiner?"
                 }
             }
         },

         methods: {
             ...mapActions('targetperformance',['getPerformances']),
             applyFilter() {
                 this.getPerformances(this.filters)
                 this.closeModal()
             },

             closeFilter() {
                 this.closeModal()
             }
         }
    }
</script>

