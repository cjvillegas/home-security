<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        :before-close="closeForm"
        width="60%"
        top="10vh">
        <div v-loading="loading">
            <el-descriptions
                v-if="scanner"
                class="margin-top"
                :column="1"
                size="mini"
                direction="horizontal"
                border>
                <div slot="title">
                    Scanner Info
                </div>

                <template
                    v-if="hasModel"
                    slot="extra">
                    <el-button
                        v-if="user && user.permissions && user.permissions.qc_tag_delete"
                        @click="deleteQcTag"
                        type="danger"
                        size="mini">
                        Delete
                    </el-button>
                </template>

                <el-descriptions-item label-class-name="w-25">
                    <template slot="label">
                        <i class="el-icon-user"></i>
                        Employee
                    </template>
                    {{ scanner.employee_name | ucWords }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template slot="label">
                        <i class="fas fa-boxes"></i>
                        Operation
                    </template>
                    {{ scanner.process_name | ucWords }}
                </el-descriptions-item>
                <el-descriptions-item label-class-name="w-25">
                    <template slot="label">
                        <i class="fas fa-calendar-alt"></i>
                        Operation Date
                    </template>
                    {{ scanner.scannedtime | fixDateByFormat }}
                </el-descriptions-item>
            </el-descriptions>

            <el-card class="box-card mt-3">
                <el-form
                    ref="qcForm"
                    :model="qcForm"
                    class="mt-3">
                    <el-descriptions
                        class="margin-top"
                        :column="1"
                        size="mini"
                        direction="horizontal"
                        border>
                        <div slot="title">
                            Quality Control Info
                        </div>
                        <el-descriptions-item
                            v-if="user"
                            label-class-name="w-25">
                            <template slot="label">
                                <i class="el-icon-user"></i>
                                QC Member
                            </template>
                            {{ user.name | ucWords }}
                        </el-descriptions-item>
                        <el-descriptions-item label-class-name="w-25">
                            <template slot="label">
                                <i class="fas fa-calendar-alt"></i>
                                Date
                            </template>
                            {{ createdDate | fixDateByFormat }}
                        </el-descriptions-item>
                        <el-descriptions-item>
                            <template slot="label">
                                <i class="fas fa-qrcode"></i>
                                QC Code
                            </template>
                            <el-form-item
                                prop="description"
                                class="mb-0">
                                <el-select
                                    v-model="qcForm.quality_control_id"
                                    clearable
                                    filterable
                                    size="mini"
                                    class="w-100">
                                    <el-option
                                        v-for="code in qcCodes"
                                        :key="code.id"
                                        :value="code.id"
                                        :label="code.qc_code | ucWords">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-descriptions-item>
                        <el-descriptions-item>
                            <template slot="label">
                                <i class="fas fa-info-circle"></i>
                                QC Code Description
                            </template>
                            {{ selectedCode ? selectedCode.description : '' }}
                        </el-descriptions-item>
                    </el-descriptions>

                    <el-form-item
                        label="Description"
                        prop="description">
                        <el-input
                            v-model="qcForm.description"
                            :rows="4"
                            placeholder="Please enter the details of the issue and or the reason why the product is required to be QC Tagged."
                            type="textarea">
                        </el-input>
                    </el-form-item>
                </el-form>
            </el-card>
        </div>

        <span
            slot="footer"
            class="dialog-footer">
		    <el-button
                @click="closeForm">
		    	Close
		    </el-button>
		    <el-button
                @click="validate"
                :disabled="disableSave"
                type="primary"
                class="btn-primary">
		    	Save
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
    import {dialog} from "../../mixins/dialog";
    import {formHelper} from "../../mixins/formHelper";

    export default {
        name: "QcTagForm",
        mixins: [dialog, formHelper],
        props: {
            user: {},
            model: {},
            scanner: {},
            qcCodes: {}
        },
        data() {
            return {
                loading: false,
                qcForm: this.getDefaultFieldValues()
            }
        },
        computed: {
            hasModel() {
                return this.model && this.model.id
            },
            dialogTitle() {
                return 'Quality Control Tag for Blind Serial ID ' + (this.scanner ? this.scanner.blindid : '')
            },
            selectedCode() {
                return this.qcCodes.find(c => c.id === this.qcForm.quality_control_id)
            },
            disableSave() {
                if (!this.qcForm.quality_control_id) {
                    return true
                }

                return !this.qcForm.description;
            },
            createdDate() {
                if (this.hasModel) {
                    this.model.created_at
                }

                return moment()
            }
        },
        methods: {
            validate() {
                if (!this.qcForm.quality_control_id) {
                    this.$notify({
                        title: 'QC Tagging',
                        message: `Please select the QC code that you want to tag to this process.`,
                        type: 'error'
                    })

                    return
                }

                if (!this.qcForm.description) {
                    this.$notify({
                        title: 'QC Tagging',
                        message: `Please specify the reason of this tag.`,
                        type: 'error'
                    })

                    return
                }

                if (this.hasModel) {
                    this.updateQcTag()

                    return
                }

                this.createNewQcTag()
            },
            createNewQcTag() {
                this.loading = true

                this.$API.Scanners.qcTag(this.qcForm)
                    .then(res => {
                        if (res.data) {
                            this.$EventBus.fire('QC_TAG_CREATE', res.data)

                            this.$notify({
                                title: 'Success',
                                message: 'Tag successfully created.',
                                type: 'success'
                            })

                            setTimeout(_ => {
                                this.closeForm()
                            }, 300)
                        }
                    })
                    .catch(err => {

                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            updateQcTag() {
                this.loading = true

                this.$API.Scanners.updateQcTag(this.model.id, this.qcForm)
                    .then(res => {
                        if (res.data) {
                            this.$EventBus.fire('QC_TAG_UPDATE', res.data)

                            this.$notify({
                                title: 'Success',
                                message: 'Tag successfully updated.',
                                type: 'success'
                            })

                            setTimeout(_ => {
                                this.closeForm()
                            }, 300)
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            deleteQcTag() {
                this.$confirm('Are you sure you want to remove the tagging of this process?', 'Confirm', {
                    confirmButtonText: "Yes, I'm Sure",
                    cancelButtonText: 'No, Not Sure',
                    type: 'error',
                    confirmButtonClass: 'el-button--danger'
                })
                    .then(_ => {
                        this.loading = true

                        this.$API.Scanners.deleteQcTag(this.model.id)
                            .then(res => {
                                if (res.data) {
                                    this.$EventBus.fire('QC_TAG_DELETE', this.scanner)

                                    this.$notify({
                                        title: 'Success',
                                        message: 'Tag successfully removed.',
                                        type: 'success'
                                    })

                                    setTimeout(_ => {
                                        this.closeForm()

                                    }, 300)
                                }
                            })
                            .catch(err => {
                                console.log(err)
                            })
                            .finally(_ => {
                                this.loading = false
                            })
                    })
                    .catch(_ => {})

            },
            initializeForm() {
                if (!this.model || !this.model.id) {
                    this.qcForm = this.getDefaultFieldValues()

                    return
                }

                this.qcForm = {
                    id: this.model.id,
                    quality_control_id: this.model.quality_control_id,
                    employee_id: this.model.employee_id,
                    user_id: this.model.user_id,
                    process_id: this.model.process_id,
                    scanner_id: this.model.scanner_id,
                    operation_date: this.model.operation_date,
                    description: this.model.description,
                }
            },
            getDefaultFieldValues() {
                return {
                    id: null,
                    quality_control_id: null,
                    employee_id: this.scanner ? this.scanner.employee_id : null,
                    user_id: this.user ? this.user.id : null,
                    process_id: this.scanner ? this.scanner.process_id : null,
                    scanner_id: this.scanner ? this.scanner.id : null,
                    operation_date: this.scanner ? this.scanner.scannedtime : null,
                    description: null,
                }
            },
            resetForm() {
                this.qcForm = this.getDefaultFieldValues()

                if (this.$refs.qcForm) {
                    setTimeout(_ => {
                        this.$refs.qcForm.clearValidate()
                    },200)
                }
            },
            closeForm() {
                this.resetForm()
                this.closeModal()
            }
        },
        watch: {
            'user.id': {
                handler() {
                    this.initializeForm()
                },
                immediate: true
            },
            'model.id': {
                handler() {
                    this.initializeForm()
                },
                immediate: true
            },
            'scanner.id': {
                handler() {
                    this.initializeForm()
                },
                immediate: true
            }
        }
    }
</script>
