<template>
    <div v-loading="loading">
        <el-steps :active="active" align-center finish-status="success">
            <el-step
                v-for="(blindId, blindKey) in selectedBlindId"
                :key="blindId.id"
                :title="stepName(blindKey)">
            </el-step>
            <el-step
                title="Order Remake Success"></el-step>
        </el-steps>
        <div
            v-for="(blindId, blindKey) in selectedBlindId"
            :key="blindId.id">
            <div
                v-if="step == blindKey+1">
                <h2
                    class="d-flex justify-content-center">
                    Remake Validation Checker for Blind Serial Number: {{ blindId.serial_id }}
                </h2>

                <span
                    class="d-flex justify-content-center">
                    Please verify your blind and make sure all the option below on the check list have been completed
                </span>
                <div class="row mt-5">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <el-checkbox-group v-model="blindValidationData[blindKey].checkedQuestions">
                            <el-checkbox
                                v-for="question in validationQuestions.QUESTIONS"
                                :key="question.key"
                                :label="question.key"
                                @change="toggleCheckbox">
                                <div v-if="question.key==1">
                                    {{ question.value }} {{ blindId.width }}
                                </div>
                                <div v-else-if="question.key==2">
                                    {{ question.value }} {{ blindId.drop }}
                                </div>
                                <div v-else-if="question.key==3">
                                    {{ question.value }} {{ blindId.fabric_range }}
                                </div>
                                <div v-else>
                                    {{ question.value }}
                                </div>
                            </el-checkbox>
                        </el-checkbox-group>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <el-footer class="float-right mb-2 mt-5">
                    <el-button
                        v-if="step == 1"
                        type="danger"
                        icon="el-icon-arrow-left"
                        @click="stepAction('cancel')">
                        Cancel
                    </el-button>
                    <el-button
                        v-else
                        type="danger"
                        icon="el-icon-arrow-left"
                        @click="stepAction('back')">
                        Back
                    </el-button>
                    <el-button
                        type="primary"
                        icon="el-icon-arrow-right"
                        @click="stepAction('next', blindId.serial_id, blindValidationData[blindKey].checkedQuestions, blindKey, blindId)">
                        Continue
                    </el-button>
                </el-footer>
            </div>
        </div>
        <remake-sucess
            v-if="step > selectedBlindId.length">
        </remake-sucess>
        <el-dialog
            :title="dialogTitle"
            :visible.sync="showRemakeValidationDialog"
            @close="closeDialog"
            width="50%">
                <span class="d-flex justify-content-center">
                    <p class="text-danger">
                    The following items on your Check list have been not vefired, are you sure you wish to continue?
                    </p>
                </span>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <el-checkbox-group
                            v-model="addedConfirmationValidations"
                            size="medium">
                            <el-checkbox
                                v-for="question in unconfirmedValidation"
                                :key="question.key"
                                :label="question.key"
                                @change="toggleCheckbox()">
                                <p v-if="question.key==1">
                                    {{ question.value }} {{ dialogBlindData.width }}
                                </p>
                                <p v-else-if="question.key==2">
                                    {{ question.value }} {{ dialogBlindData.drop }}
                                </p>
                                <p v-else-if="question.key==3">
                                    {{ question.value }} {{ dialogBlindData.fabric_range }}
                                </p>
                                <p v-else>
                                    {{ question.value }}
                                </p>
                            </el-checkbox>
                        </el-checkbox-group>
                    </div>
                    <div class="col-md-3"></div>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button type="danger" @click="cancelChanges">No</el-button>
                    <el-button type="primary" @click="confirmChanges">Confirm</el-button>
                </span>
        </el-dialog>
    </div>
</template>

<script>
    import { mapActions, mapGetters, mapMutations } from 'vuex'
    import * as validationQuestions from '../../../constants/remake-validation-questions'
    export default {
        name: "RemakeValidationProcess",
        data() {
            return {
                active: 0,
                step: 1,
                questions: [],
                validationQuestions,
                blindValidationData: [],
                currentBlindId: null,
                showRemakeValidationDialog: false,

                unconfirmedValidation: [],
                dialogBlindData: [],
                dialogBlindId: null,
                addedConfirmationValidations: [],
                dialogKeyIndex: null
            }
        },
        created() {
            this.blindValidationData = this.validationFormData
        },
        computed: {
            ...mapGetters('remakechecker', ['selectedOrderNo', 'selectedBlindId', 'loading']),
            validationFormData() {
                let data = []
                this.selectedBlindId.forEach(blind => {
                    data.push({
                        'order_no': this.selectedOrderNo,
                        'serial_id': blind.serial_id,
                        'checkedQuestions': []
                    })
                })

                return data
            },

            dialogTitle() {
                return `Confirmation for ${this.dialogBlindId}`
            }
        },
        methods: {
            ...mapActions('remakechecker', ['backToMainScreen', 'saveOrderRemake']),
            ...mapMutations('remakechecker', ['setBlindValidationData', 'setAnsweredQuestions', 'setLoading']),
            toggleCheckbox(){

            },
            stepAction(action, serialId = null, answers = null, keyIndex = null, blindData) {
                if (action == 'next') {
                    // No checklist checked
                    if (answers.length == 0) {
                        this.$notify({
                            title: 'Remake Checker Error',
                            message: 'Select atleast one checklist',
                            type: 'error'
                        })
                        return
                    }
                    //Not all checkboxes are selected
                    if (answers.length != validationQuestions.QUESTIONS.length) {
                        this.unconfirmedValidation = this.validationQuestions.QUESTIONS.filter(
                            question => !answers.includes(question.key)
                        )
                        this.dialogBlindId = serialId,
                        this.dialogBlindData = blindData,
                        this.dialogKeyIndex = keyIndex
                        this.showRemakeValidationDialog = true
                        return
                    }
                    this.resetDialogValues()
                    this.active ++
                    this.step ++
                    //Last step
                    this.lastStepSavePayload()
                }
                if (action == 'back') {
                    this.active --
                    this.step --
                }

                if (action == 'cancel') {
                    this.backToMainScreen()
                    this.$notify({
                        title: 'Remake Checker',
                        message: 'Action Cancelled',
                        type: 'warning'
                    })
                }
            },
            stepName(key) {
                return `Blind ${key + 1}`
            },
            closeDialog() {
                this.showRemakeValidationDialog = false
                this.resetDialogValues()
            },
            //for dialog
            confirmChanges() {
                this.setLoading(true)
                if (this.blindValidationData[this.dialogKeyIndex].length != validationQuestions.QUESTIONS.length) {
                    if (this.addedConfirmationValidations.length != this.unconfirmedValidation.length) {
                        this.$prompt('Please describe below the reason why some item(s) have been not verified', 'Confirmation', {
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel',
                            inputErrorMessage: 'Input required',
                            inputType: 'textarea'
                        }).then(({ value }) => {
                            this.addedConfirmationValidations.forEach(x => {
                                this.blindValidationData[this.dialogKeyIndex].checkedQuestions.push(x)
                            })
                            this.blindValidationData[this.dialogKeyIndex].reason = value
                            this.resetDialogValues()
                            this.active ++
                            this.step ++
                            this.closeDialog()
                            //Last step
                            this.lastStepSavePayload()
                            setTimeout(_ => {
                                this.setLoading(false)
                            }, 1500)

                        }).catch((err) => {
                            this.$message({
                                type: 'info',
                                message: err
                            });
                        });
                    } else {
                        this.addedConfirmationValidations.forEach(x => {
                            this.blindValidationData[this.dialogKeyIndex].checkedQuestions.push(x)
                        })
                        this.active ++
                        this.step ++
                        this.resetDialogValues()
                        this.closeDialog()
                        //Last step
                        this.lastStepSavePayload()
                        setTimeout(_ => {
                            this.setLoading(false)
                        }, 1500)

                    }
                    //Last step
                    this.lastStepSavePayload()
                }
            },
            cancelChanges() {

            },
            lastStepSavePayload() {
                if (this.step > this.selectedBlindId.length) {
                    this.saveOrderRemake(this.blindValidationData)
                }
            },
            resetDialogValues() {
                this.unconfirmedValidation = [],
                this.dialogBlindData = [],
                this.dialogBlindId = null,
                this.addedConfirmationValidations = [],
                this.dialogKeyIndex = null
            }
        }
    }
</script>
