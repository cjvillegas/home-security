<template>
    <div>
        <el-card class="box-card">
            <div class="d-flex align-items-center">
                <el-button
                    @click="backToList">
                    <i class="fas fa-arrow-left"></i> Back to List
                </el-button>

                <h4 class="mb-0 ml-3">{{ sequenceName }}</h4>
            </div>
        </el-card>

        <el-card class="box-card mt-3">
            <div class="text-center mr-auto ml-auto w-75">
                <div>
                    <el-tag
                        type="success"
                        effect="dark">
                        {{ pageData.processSequence.name | ucWords }} Start
                    </el-tag>
                </div>
                <div class="text-muted">
                    <i class="fas fa-arrow-down"></i>
                </div>

                <div
                    class="text-center"
                    v-for="(step, index) in sortedSteps"
                    :key="step.id">
                    <el-card
                        class="box-card position-relative">
                        <div class="d-flex justify-content-center align-items-center">
                            <el-avatar icon="el-icon-picture-outline"></el-avatar>
                            <h5 class="mb-0 ml-2">{{ step.process.name | ucWords }}</h5>
                        </div>

                        <div
                            style="right: 25px; top: 30%;"
                            class="position-absolute">
                            <el-button-group>
                                <el-tooltip
                                    v-if="totalStepsCount > 1 && index !== totalStepsCount - 1"
                                    content="Add a step below this step."
                                    placement="top">
                                    <el-button
                                        @click="stageStep(step, sortedSteps[index + 1], step.order)">
                                        <i class="fas fa-plus-circle"></i>
                                    </el-button>
                                </el-tooltip>

                                <el-tooltip
                                    content="Move this step up."
                                    placement="top">
                                    <el-button
                                        v-if="step.order !== 1"
                                        @click="moveStep(step, sortedSteps[index - 1], 'up')">
                                        <i class="fas fa-arrow-up"></i>
                                    </el-button>
                                </el-tooltip>

                                <el-tooltip
                                    content="Move this step down."
                                    placement="top">
                                    <el-button
                                        v-if="step.order !== totalStepsCount"
                                        @click="moveStep(step, sortedSteps[index + 1], 'down')">
                                        <i class="fas fa-arrow-down"></i>
                                    </el-button>
                                </el-tooltip>

                                <el-tooltip
                                    content="Delete this step."
                                    placement="top">
                                    <el-button
                                        @click="deleteStep(step)">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </el-button>
                                </el-tooltip>
                            </el-button-group>
                        </div>
                    </el-card>

                    <div class="text-muted">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>

                <div>
                    <el-button
                        round
                        type="primary"
                        @click="stageStep(null, null)">
                        <i class="fas fa-plus-circle"></i> Add Step
                    </el-button>
                </div>
            </div>
        </el-card>

        <el-dialog
            @close="closeProcessSelector"
            :visible.sync="showProcessSelector"
            title="Add New Step"
            top="5vh"
            custom-class="process-type-selector">
            <div class="p-2">
                <el-input
                    v-model="searchString"
                    class="w-100"
                    placeholder="Search processes...">
                </el-input>
            </div>

            <el-divider class="p-2"></el-divider>

            <el-alert
                title="Please select a process that you want to add in this sequence."
                :closable="false"
                type="success"
                effect="dark"
                class="p-2">
            </el-alert>

            <div class="process-selection-container mt-3 p-2">
                <el-card
                    class="box-card mb-3 cursor-pointer process-steps-item"
                    v-for="process in filteredProcesses"
                    :key="process.id"
                    @click.native="addStep(process)">
                    <div class="d-flex align-items-center">
                        <el-avatar icon="el-icon-picture-outline"></el-avatar>
                        <h5 class="mb-0 ml-2">{{ process.name | ucWords }}</h5>
                    </div>
                </el-card>
            </div>

        </el-dialog>
    </div>
</template>

<script>
    import cloneDeep from 'lodash/cloneDeep'
    export default {
        name: "ProcessSequenceSteps",
        props: {
            pageData: {
                required: true,
                type: Object
            }
        },
        data() {
            return {
                loading: false,
                fetchLoading: false,
                showProcessSelector: false,
                searchString: null,
                processSequenceSteps: [],
                linkForm: {
                    process_sequence_id: this.pageData.processSequence.id,
                    process_id: null,
                    previous_step_id: null,
                    next_step_id: null,
                    order: 0
                }
            }
        },
        created() {
            this.fetchProcessSteps(this.pageData.processSequence.id)
        },
        methods: {
            stageStep(prevStep, nextStep, currentOrder) {
                if (currentOrder === undefined) {
                    prevStep = this.processSequenceSteps[this.totalStepsCount - 1]
                }

                this.linkForm.previous_step_id = prevStep ? prevStep.id : null
                this.linkForm.next_step_id = nextStep ? nextStep.id : null
                this.linkForm.order = currentOrder !== undefined ? currentOrder + 1 : this.totalStepsCount

                this.showProcessSelector = true
            },
            addStep(process) {
                this.$confirm('Add this process to the sequence?', 'Confirm', {
                    confirmButtonText: 'Add Step',
                    cancelButtonText: 'Nope',
                    type: 'primary'
                })
                .then(_ => {
                    let form = cloneDeep(this.linkForm)
                    form.process_id = process.id

                    this.loading = true

                    this.$API.ProcessSequenceLink.store(form.process_sequence_id, form)
                        .then(res => {
                            if (res.data) {
                                this.fetchProcessSteps(form.process_sequence_id)

                                setTimeout(_ => {
                                    this.showProcessSelector = false
                                },200)
                            }
                        })
                        .catch(err => {
                            if (err.response.status === 422) {
                                this.$notify({
                                    title: 'Process Sequence',
                                    message: 'The given data is invalid.',
                                    type: 'error'
                                })
                            }

                            console.log(err)
                        })
                        .finally(_ => {
                            this.loading = false
                        })
                })
                .catch(_ => {})
            },
            deleteStep(step) {
                this.$confirm('Are you sure you want to delete this step?', 'Confirm', {
                    confirmButtonText: 'Yes, I\'m sure',
                    cancelButtonText: 'No, Thanks',
                    type: 'warning',
                    confirmButtonClass: 'el-button--danger'
                })
                .then(_ => {
                    this.loading = true

                    this.$API.ProcessSequenceLink.delete(step.process_sequence_id, step.id)
                        .then(res => {
                            if (res.data) {
                                this.fetchProcessSteps(step.process_sequence_id)
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
            moveStep(currentStep, affectedNode, direction) {
                if (direction === 'down') {
                    currentStep.order++
                    if (affectedNode) {
                        affectedNode.order--
                    }
                }

                if (direction === 'up') {
                    currentStep.order--
                    if (affectedNode) {
                        affectedNode.order++
                    }
                }

                this.moveStepOrder(currentStep, affectedNode, direction)
            },
            moveStepOrder(currentStep, affectedNode, direction) {
                this.loading = true

                this.$API.ProcessSequenceLink.moveStepOrder(currentStep.process_sequence_id, currentStep.id, direction)
                    .then(res => {
                        if (Array.isArray(res.data)) {
                            this.processSequenceSteps = cloneDeep(res.data)
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },
            fetchProcessSteps(processSequenceId) {
                this.fetchLoading = true

                this.$API.ProcessSequenceLink.getProcessSequenceSteps(processSequenceId)
                    .then(res => {
                        this.processSequenceSteps = cloneDeep(res.data)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.fetchLoading = false
                    })
            },
            closeProcessSelector() {
                this.linkForm.order = 0
                this.linkForm.previous_step_id = null
                this.linkForm.next_step_id = null
                this.showProcessSelector = false
            },
            backToList() {
                window.location.href = `/admin/process-sequence`
            }
        },
        computed: {
            sortedSteps() {
                return this.processSequenceSteps.sort((stepOne, stepTwo) => {
                    if (stepOne.order < stepTwo.order) {
                        return -1
                    }

                    if (stepOne.order > stepTwo.order) {
                        return 1
                    }

                    return 0
                })
            },
            sequenceName() {
                if (this.pageData.processSequence && this.pageData.processSequence.name) {
                    return this.$StringService.ucwords(this.pageData.processSequence.name)
                }

                return ''
            },
            totalStepsCount() {
                return Array.isArray(this.processSequenceSteps) ? this.processSequenceSteps.length : 0
            },
            filteredProcesses() {
                let processes = cloneDeep(this.pageData.processes)
                    .filter(pr => !this.processSequenceSteps.some(prs => prs.process_id === pr.id))

                if (this.searchString) {
                    let query = this.searchString.toLowerCase()

                    processes = processes.filter(pr => {
                        return pr.name.toLowerCase().indexOf(query) > -1;
                    })
                }

                return processes
            }
        }
    }
</script>

<style lang="scss">
    .process-selection-container {
        max-height: 60vh;
        overflow: hidden;
        overflow-y: scroll;
    }

    .process-type-selector {

    }
</style>
