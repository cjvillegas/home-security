<template>
    <div>
        <global-page-header title="Remake Checker"></global-page-header>
        <el-card
            class="box-card mt-3">
            <!-- Landing page, search -->
            <remake-search
                v-if="activeForm == 'search'">
            </remake-search>

            <!-- Orders/Blinds List -->
            <remake-orders
                v-if="activeForm == 'ordersList'">
            </remake-orders>

            <!-- Blind Validation Process -->
            <remake-validation-process
                v-if="activeForm == 'blindValidationProcess'">
            </remake-validation-process>
        </el-card>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
    export default {
        name: "RemakeChecker",
        computed: {
            ...mapGetters('remakeChecker', ['activeForm'])
        }
    }
</script>
