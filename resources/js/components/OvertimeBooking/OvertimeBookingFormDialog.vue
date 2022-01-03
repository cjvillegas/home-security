<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        :before-close="closeForm"
        width="20%">
        <el-form
            ref="slotForm"
            :model="slotForm"
            :rules="rules"
            v-loading="loading"
            label-position="left">
            <el-form-item
                label="Select Date(s)"
                prop="dates">
                <el-date-picker
                    type="dates"
                    v-model="slotForm.dates"
                    placeholder="Pick one or more dates">
                </el-date-picker>
            </el-form-item>
            <el-form-item
                label="Required Hours"
                prop="working_hours">
                <el-select v-model="slotForm.working_hours" placeholder="Select">
                    <el-option
                        v-for="item in workingHours"
                        :key="item.value"
                        :label="item.value"
                        :value="item.value">
                    </el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <span
            slot="footer"
            class="dialog-footer">
            <el-button
                @click="closeForm">
                Cancel
            </el-button>
            <el-button
                type="primary"
                @click="validate">
                Save
            </el-button>
        </span>
    </el-dialog>
</template>

<script>
    import { mapActions } from 'vuex'
    import {dialog} from "../../mixins/dialog"
    import {formHelper} from "../../mixins/formHelper"
    export default {
        name: "OvertimeBookingFormDialog",

        mixins: [dialog, formHelper],

        data() {
            return {
                loading: false,
                slotForm: {
                    dates: [],
                    working_hours: null,
                },
                rules: {
                    dates: {required: true, message: 'Available Date(s) field is required.'},
                    working_hours: {required: true, message: 'Working Hours field is required.'},
                },
                workingHours: [
                    {'value': 2},
                    {'value': 4},
                    {'value': 6},
                    {'value': 8},
                    {'value': 10},
                    {'value': 12},
                    {'value': 14},
                    {'value': 16},
                ]
            }
        },

        computed: {
            dialogTitle() {
                return "Add New Overtime Slot"
            }
        },

        methods: {
            validate() {
                this.$refs.slotForm.validate(valid => {
                    if (valid) {
                        this.resetErrors()

                        this.saveSlot(this.slotForm)
                        .then((res) => {
                            this.$notify({
                                title: 'Success',
                                message: res.data.message,
                                type: 'success'
                            })

                            setTimeout(_ => {
                                this.closeForm()
                                this.getSlots()
                            },200)
                        })
                    }
                })
            },

            getDefaultFieldValues() {
                return {
                    dates: null,
                    working_hours: null,
                }
            },
            resetForm() {
                this.slotForm = this.getDefaultFieldValues
                this.resetErrors()
            },

            closeForm(ignoreChecker = false) {
                this.resetForm()
                this.closeModal()
            },

            ...mapActions('overtimeBooking', ['saveSlot', 'getSlots'])
        }
    }
</script>
