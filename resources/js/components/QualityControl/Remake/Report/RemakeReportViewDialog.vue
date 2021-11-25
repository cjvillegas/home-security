<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        :before-close="closeForm"
        width="60%">
        <el-row
            :gutter="12">
            <el-col
                :span="12"
                v-for="(blind, blindKey) in viewOrderRemake.validated_blinds"
                :key="blindKey">
                <el-card
                    class="box-card border border-secondary mt-2"
                    :body-style="{height: '380px'}">
                    <div class="d-flex">
                        <div class="ml-auto">
                            <u>
                                Serial ID: {{ blind.blind_id }}
                            </u>
                        </div>
                    </div>
                    <el-checkbox
                        v-for="question in validationQuestions.QUESTIONS"
                        :key="question.key"
                        :label="question.key"
                        :checked="isChecked(question.key, blind.question_key)"
                        text-color="#199f6b"
                        disabled>
                        <div v-if="question.key==1">
                            {{ question.value }} {{ blind.blind.width }}
                        </div>
                        <div v-else-if="question.key==2">
                            {{ question.value }} {{ blind.blind.drop }}
                        </div>
                        <div v-else-if="question.key==3">
                            {{ question.value }} {{ blind.blind.fabric_range }}
                        </div>
                        <p v-else>
                            {{ question.value }}
                        </p>
                    </el-checkbox>
                    <div>
                        <el-button
                            class="mt-2"
                            v-if="isPartiallyVerified(validationQuestions.QUESTIONS, blind.question_key)"
                            type="default"
                            @click="viewReason(blind.reason, blind.blind_id)">
                            View Reason
                        </el-button>
                    </div>
                    <span class="mt-5">
                        <p style="magin: 0px">
                            Validated By: {{ viewOrderRemake.user.name }}
                        </p>
                        <p style="magin: 0px">
                            Validated At: {{ viewOrderRemake.created_at | fixDateTimeByFormat('MMM DD, YYYY HH:mm:ss') }}
                        </p>
                    </span>
                </el-card>
            </el-col>
        </el-row>
    </el-dialog>
</template>

<script>
    import { mapGetters, mapMutations } from 'vuex'
    import * as validationQuestions from '../../../../constants/remake-validation-questions'
    import {dialog} from "../../../../mixins/dialog"
    export default {
        name: "RemakeReportViewDialog",
        mixins: [dialog],
        data() {
            return {
                validationQuestions,
                tanga: true
            }
        },
        computed: {
            ...mapGetters('remakechecker', ['viewOrderRemake', 'verifiedBy']),
            dialogTitle() {
                return `Remake Report`
            },
        },
        methods: {
            ...mapMutations('remakechecker', ['setViewOrderRemake']),
            closeForm() {
                this.setViewOrderRemake([])
                this.closeModal()
            },
            isChecked(questionKey, answers) {
                let selected = false
                answers.forEach(answer => {
                    if (answer == questionKey) {
                        selected = true
                    }
                })
                return selected
            },
            viewReason(data, blindid) {
                this.$alert(data, `Blind Serial No: ${blindid} Validation Report Reason:`, {
                    confirmButtonText: 'OK',
                    center: true,
                    type: 'warning',
                    iconClass: 'fa fa-info-circle'
                });
            },
            isPartiallyVerified(questions, answers)
            {
                return questions.length > answers.length
            }
        }
    }
</script>
