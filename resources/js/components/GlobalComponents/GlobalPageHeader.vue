<template>
    <el-card class="box-card">
        <h4 class="mb-0">{{ title | ucWords }}</h4>
    </el-card>
</template>

<script>
    export default {
        name: "GlobalPageHeader",
        props: {
            title: {
                type: String,
                required: true
            }
        }
    }
</script>
