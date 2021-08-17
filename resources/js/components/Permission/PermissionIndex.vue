<template>
    <div>
        <el-card class="box-card">
            <h4 class="mb-0">Permission List</h4>
        </el-card>

        <el-card class="box-card mt-3">
            <div class="d-flex">
                <div>
                    <el-input
                        v-model="filters.searchString"
                        clearable
                        placeholder="Search Permission..."
                        style="width: 250px">
                    </el-input>
                </div>

                <div class="ml-auto">
                    <el-button
                        type="primary"
                        @click="addNew">
                        <i class="fas fa-plus"></i> Add Permission
                    </el-button>
                </div>
            </div>

            <el-table
                :data="permissions"
                class="w-100"
                fit>

            </el-table>
        </el-card>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                filters: {
                },
                permission: []
            }
        },

        methods: {
            addNew() {

            }
        }
    }
</script>

<style>

</style>
