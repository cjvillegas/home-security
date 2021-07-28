<template>
    <el-card class="box-card">
        <div v-loading="loading"></div>
        <div class="d-flex">
            <div class="ml-auto">
                <el-button
                    type="primary">
                    <i class="fas fa-plus"></i> Add Stock Item
                </el-button>
            </div>
        </div>
        <el-table>
        </el-table>
    </el-card>
</template>

<script>
export default {
    props: {
        user: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            loading: false
        }
    }
}
</script>

<style>

</style>
