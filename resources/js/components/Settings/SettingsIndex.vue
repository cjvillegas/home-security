<template>
    <el-tabs
        @tab-click="tabChange"
        type="border-card"
        v-model="activeTab">
        <el-tab-pane
            v-if="pageData.user.permissions.process_categories_access"
            label="Process Categories"
            name="process-categories"
            lazy>
            <settings-process-categories :user="pageData.user"></settings-process-categories>
        </el-tab-pane>

        <div
            v-if="!pageData.user.permissions.process_categories_access"
            style="height: 300px; font-size: 16px"
            class="d-flex justify-content-center align-items-center font-weight-bolder">
            It seems like you don't have any permission to access this page. If you think that this is a problem, please contact your administrator or configure your permissions here;
            <a href="/admin/roles" target="_blank" class="ml-1 mr-1">roles</a> and <a href="/admin/permissions" target="_blank" class="ml-1">permission</a>.
        </div>
    </el-tabs>
</template>

<script>
export default {
    name: "SettingIndex",
    props: {
        pageData: {}
    },
    data() {
        return {
            activeTab: 'process-categories'
        }
    },
    methods: {
        tabChange() {
            if (tab.name) {
                window.history.replaceState('', `Production | Settings`, `#/${tab.name}`)
            }
        }
    }
}
</script>

<style scoped>

</style>
