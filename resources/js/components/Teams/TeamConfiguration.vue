<template>
    <div v-loading="loading">
        <el-card class="box-card">
            <div class="d-flex align-items-center">
                <el-button
                    @click="backToList">
                    <i class="fas fa-arrow-left"></i> Back to List
                </el-button>

                <h4 class="mb-0 ml-3">{{ teamName | ucWords }}</h4>
            </div>
        </el-card>

        <el-card class="box-card mt-3">
            <div v-if="team_details">
                <el-descriptions
                    class="margin-top"
                    :column="1"
                    size="medium"
                    direction="vertical"
                    border>
                    <template slot="title">
                        {{ `${teamName} Information` | ucWords }}
                    </template>

                    <template slot="extra">
                        <el-button
                            @click="deleteTeam"
                            type="danger"
                            size="small">
                            Delete
                        </el-button>
                    </template>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-address-card"></i>
                            ID
                        </template>
                        {{ team_details.id | numFormat }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="el-icon-user"></i>
                            Name
                        </template>
                        {{ teamName | ucWords }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-crosshairs"></i>
                            Target
                        </template>
                        {{ team_details.target | numFormat }}
                    </el-descriptions-item>

                    <el-descriptions-item>
                        <template slot="label">
                            <i class="fas fa-folder-open"></i>
                            Folders
                        </template>

                        <span v-if="!team_details.folder_names || !team_details.folder_names.length">
                            --:--
                        </span>

                        <el-tag
                            v-else
                            v-for="folder in team_details.folder_names"
                            :key="folder"
                            type="primary"
                            class="ml-2">
                            {{ folder | ucWords }}
                        </el-tag>
                    </el-descriptions-item>
                </el-descriptions>
            </div>

            <el-empty
                v-else
                description="No Team Found. The team might be deleted or not existing in your company.">
                <el-button
                    @click="backToList">
                    Back to List
                </el-button>
            </el-empty>
        </el-card>
    </div>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep"
    import axios from "axios";

    export default {
        name: "TeamConfiguration",

        props: {
            team: {}
        },

        data() {
            return {
                loading: false,
                team_details: cloneDeep(this.team)
            }
        },

        computed: {
            teamName() {
                return this.team_details ? this.team_details.name : ''
            },
        },

        methods: {
            deleteTeam() {

            },

            backToList() {
                this.$router.push({name: 'Team List'})
            }
        },

        beforeRouteEnter(to, from, next) {
            if (to.params && !to.params.team) {
                next({replace: true, name: 'Team List'})
            }

            next()
        },
    }
</script>
