<template>
    <div>
        <global-page-header title="Shift Performance"></global-page-header>
        <el-card class="box-card mt-2">
            <div class="row">
                <div class="col-md-4"></div>
                    <div class="col-md-5">
                        <el-form label-width="250px">
                            <el-form-item
                                label="Please select the Department: ">
                                <el-select v-model="selectedDepartments" placeholder="please select your zone">
                                    <el-option
                                        v-for="(department, departmentKey) in departments.DEPARTMENTS"
                                        :label="department.value"
                                        :value="department.key"
                                        :key="departmentKey">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item
                                label="Please select the Shift: ">
                                <el-select placeholder="please select your zone">
                                    <el-option label="Zone one" value="shanghai"></el-option>
                                    <el-option label="Zone two" value="beijing"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item
                                label="Please select the Date Range: ">
                                <el-select placeholder="please select your zone">
                                    <el-option label="Zone one" value="shanghai"></el-option>
                                    <el-option label="Zone two" value="beijing"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-form>
                    </div>
                <div class="col-md-3"></div>
            </div>

        </el-card>
    </div>
</template>

<script>
    import * as departments from '../../../constants/departments'
    export default {
        data() {
            return {
                departments,
                selectedDepartments: [],
            }
        }
    }
</script>
