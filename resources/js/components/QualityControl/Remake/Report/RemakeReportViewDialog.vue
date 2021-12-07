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
                <table class="table mt-2">
                    <thead>
                        <tr class="table-info">
                            <th>
                                Serial ID: {{ blind.blind_id }} -
                            <el-button
                                v-if="isPartiallyVerified(validationQuestions.QUESTIONS, blind.question_key)"
                                type="warning"
                                @click="viewReason(blind.reason, blind.blind_id)">
                                View Reason
                            </el-button>
                            <el-button
                                v-else
                                disabled
                                type="default">
                                View Reason
                            </el-button>

                            <p style="magin: 0px">Validated By: {{ viewOrderRemake.user.name }} </p>
                            <p style="magin: 0px">Validated At: {{ viewOrderRemake.created_at | fixDateTimeByFormat('MMM DD, YYYY HH:mm:ss') }} </p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="question in validationQuestions.QUESTIONS"
                            :key="question.key"
                            v-bind:class="[isChecked(question.key, blind.question_key) ? 'table-success' : 'table-danger']">
                            <td>
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
                            </td>
                        </tr>
                    </tbody>
                </table>
                <span class="mt-5">


                    </p>
                    <p style="magin: 0px">

                    </p>
                </span>
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
            ...mapGetters('remakeChecker', ['viewOrderRemake', 'verifiedBy']),
            dialogTitle() {
                return `Remake Report`
            },
        },
        methods: {
            ...mapMutations('remakeChecker', ['setViewOrderRemake']),
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
