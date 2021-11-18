<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="dialogTitle"
        :before-close="closeForm"
        width="60%">
        <el-row>
            <el-col
                :span="12"
                v-for="(blind, blindKey) in viewOrderRemake"
                :key="blindKey">
                <el-card class="box-card border border-success">
                    <el-checkbox
                        v-for="question in validationQuestions.QUESTIONS"
                        :key="question.key"
                        :label="question.key"
                        :checked="isChecked(question.key, blind.question_key)">
                        <div v-if="question.key==1">
                            {{ question.value }} {{ blind.blind.width }}
                        </div>
                        <div v-else-if="question.key==2">
                            {{ question.value }} {{ blind.blind.drop }}
                        </div>
                        <div v-else-if="question.key==3">
                            {{ question.value }} {{ blind.blind.fabric_range }}
                        </div>
                        <div v-else>
                            {{ question.value }}
                        </div>
                    </el-checkbox>
                    <div v-if="isPartiallyVerified(validationQuestions.QUESTIONS, blind.question_key)">
                        <el-button
                            type="primary">
                            Tanga
                        </el-button>
                    </div>
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
            ...mapGetters('remakechecker', ['viewOrderRemake']),
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
            isPartiallyVerified(questions, answers)
            {
                return questions.length > answers.length
            }
        }
    }
</script>
