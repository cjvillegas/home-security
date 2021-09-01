<template>
    <el-popover
        placement="bottom-start"
        width="350"
        trigger="click"
        class="mr-2">
        <div class="el-popover__title">
            Filters
        </div>

        <el-divider class="mt-1 mb-1"></el-divider>

        <!--The filter components-->
        <slot></slot>

        <slot
            v-if="hasCustomReferenceSlot"
            name="reference"
            slot="reference">
        </slot>

        <el-button
            v-else
            slot="reference">
            <i class="fas fa-filter"></i>
        </el-button>
    </el-popover>
</template>

<script>
    export default {
        name: "GlobalFilterBox",

        computed: {
            hasCustomReferenceSlot() {
                return !!this.$slots.reference
            }
        }
    }
</script>
