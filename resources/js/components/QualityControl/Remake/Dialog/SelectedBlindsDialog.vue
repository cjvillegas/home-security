<template>
    <el-dialog
        title="Selected Blinds Confirmation"
        :visible.sync="showDialog"
        :before-close="closeDialog"
        width="40%"
        center>
        <span>You have selected the following Blind(s)</span>
        <div
            v-for="blindId in selectedBlindId"
            :key="blindId.id"
            class="blindId">
            {{ blindId.serial_id }}
        </div>
        <span>Are you sure you wish to create a Remake Validation Checker?</span>
        <span slot="footer" class="dialog-footer">
            <el-button type="danger" @click="cancelRemake">No</el-button>
            <el-button type="primary" @click="proceedRemake">Confirm</el-button>
        </span>
    </el-dialog>
</template>

<style scoped>
    span, .blindId {
        display: table;
        margin: 0 auto;
        font-size: 15px;
    }
</style>
<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'
    import {dialog} from "../../../../mixins/dialog"

    export default {
        name: "SelectedBlindsDialog",
        mixins: [dialog],
        computed: {
            ...mapGetters('remakechecker', ['selectedBlindId'])
        },
        methods: {
            ...mapActions('remakechecker', ['backToMainScreen']),
            ...mapMutations('remakechecker', ['setActiveForm']),
            proceedRemake() {
                this.setActiveForm('blindValidationProcess')
                this.$notify({
                    title: 'Remake Checker',
                    message: 'Action Confirmed',
                    type: 'success'
                })
            },
            cancelRemake() {
                this.backToMainScreen()
                setTimeout(_ => {
                    this.closeModal()
                }, 300)

                this.$notify({
                    title: 'Remake Checker',
                    message: 'Action Cancelled',
                    type: 'warning'
                })
            },
            closeDialog() {
                this.closeModal()
            }
        }
    }
</script>
