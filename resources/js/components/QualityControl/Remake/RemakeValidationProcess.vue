<template>
    <div>
        <el-steps :active="active" finish-status="success">
            <el-step
                v-for="(blindId, blindKey) in selectedBlindId"
                :key="blindId.id"
                :title="stepName(blindKey)">
            </el-step>
            <el-step
                title="Verified"></el-step>
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
                <el-checkbox-group
                    v-model="questions"
                    class="d-flex justify-content-center">
                    <el-checkbox label="Option A"></el-checkbox>
                    <el-checkbox label="Option B"></el-checkbox>
                    <el-checkbox label="Option C"></el-checkbox>
                    <el-checkbox label="disabled" disabled></el-checkbox>
                    <el-checkbox label="selected and disabled" disabled></el-checkbox>
                </el-checkbox-group>
                <el-footer class="float-right mb-5 mt-5">
                    <el-button
                        type="primary"
                        icon="el-icon-arrow-left"
                        @click="nextStep">
                        Continue
                    </el-button>
                </el-footer>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .centered-process {
        display: table;
        margin: 0 auto;
    }
</style>
<script>
    import { mapActions, mapGetters } from 'vuex'
    import * as ValidationQuestions from '../../../constants/remake-validation-questions'
    export default {
        name: "RemakeValidationProcess",
        data() {
            return {
                active: 0,
                step: 1,
                questions: []
            }
        },
        computed: {
            ...mapGetters('remakechecker', ['selectedBlindId']),
        },
        methods: {
            nextStep() {
                this.active ++
                this.step ++
            },
            stepName(key) {
                return "Blind"+ key
            }
        }
    }
</script>
