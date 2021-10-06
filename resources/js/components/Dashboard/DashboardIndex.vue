<template>
    <div>
        <h2>Welcome to Style</h2>

        <el-tabs
            v-model="activeTab"
            type="border-card"
            <el-tab-pane
                name="manufactured-blinds"
                label="Manufactured Blinds"
                lazy>
                <dashboard-manufactured-blinds></dashboard-manufactured-blinds>
            </el-tab-pane>
            <el-tab-pane
                name="despatch-department"
                label="Despatch Department"
                lazy>
                <dashboard-despatch-department></dashboard-despatch-department>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
export default {
    name: "DashboardIndex",
    data() {
        return {
            activeTab: 'despatch-department'
        }
    }
}
</script>
