<template>
    <el-dialog
        title="Confirmation"
        :visible.sync="showDialog"
        :before-close="closeDialog"
        width="40%">
        <div class="float-right">
            Blind ID serial number: {{ blindId }}
        </div>
        <span class="d-flex justify-content-center">
            <p class="text-danger">
               The following items on your Check list have been not vefired, are you sure you wish to continue?
            </p>
        </span>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-checkbox-group v-model="addedConfirmationValidations">
                    <el-checkbox
                        v-for="question in unconfirmedValidation"
                        :key="question.key"
                        :label="question.key"
                        @change="toggleCheckbox()">
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
        <span slot="footer" class="dialog-footer">
            <el-button type="danger" @click="cancelChanges">No</el-button>
            <el-button type="primary" @click="confirmChanges">Confirm</el-button>
        </span>
    </el-dialog>
</template>

<style scoped>
    .centered-process {
        display: table;
        margin: 0 auto;
    }
</style>
<script>
    import {dialog} from "../../../../mixins/dialog"
    import * as validationQuestions from '../../../../constants/remake-validation-questions'
    export default {
        name: "RemakeValidationDialog",
        mixins: [dialog],
        props: {
            confirmedQuestions: {
                type: Array
            },
            blindId: {
                default: null
            },
            keyIndex: {
                default: null
            }
        },
        data() {
            return {
                validationQuestions,
                addedConfirmationValidations: []
            }
        },
        computed: {
            unconfirmedValidation() {
                let data = this.validationQuestions.QUESTIONS.filter(
                    question => !this.confirmedQuestions.includes(question.key)
                )
                return data
            }
        },
        methods: {
            confirmChanges() {
                this.$EventBus.fire('CONFIRM_CHANGES', this.addedConfirmationValidations, keyIndex)
            },
            cancelChanges() {

            },
            closeDialog() {
                this.closeModal()
            }
        }
    }
</script>
