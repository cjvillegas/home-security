<template>
    <div class="work-analytics">
        <el-tabs
            @tab-click="tabChange"
            type="border-card"
            v-model="activeTab">
            <el-tab-pane
                label="Hourly Work Analytics"
                name="hourly-work-analytics"
                lazy>
                <hourly-work-analytics></hourly-work-analytics>
            </el-tab-pane>
            <el-tab-pane
                label="Daily Work Analytics"
                name="daily-work-analytics"
                lazy>
                <daily-work-analytics></daily-work-analytics>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    export default {
        name: "WorkAnalyticsIndex",

        data() {
            return {
                activeTab: 'hourly-work-analytics'
            }
        },

        created() {
            this.selectTabBasedOnHash()
        },

        methods: {
            tabChange(tab) {
                if (tab.name) {
                    window.history.replaceState('', `Production | Reports`, `#/${tab.name}`)
                }
            },
            selectTabBasedOnHash() {
                let tabName = this.$route.path.split('/')
                this.activeTab = tabName[1] ? tabName[1] : this.activeTab
            },
        }

    }
</script>

<style scoped>

</style>
