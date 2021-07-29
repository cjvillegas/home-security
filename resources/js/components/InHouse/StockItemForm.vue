<template>
    <el-card>
        <h1>{{ formTitle }}</h1>
        <el-form>
            <el-row :gutter="20">
                <el-col
                    :span="12"
                    >
                    <el-form-item
                        label="Stock Code">
                        <el-input></el-input>
                    </el-form-item>
                </el-col>

                <el-col
                    :span="12">
                    <el-form-item
                        label="Stock Code">
                        <el-input></el-input>
                    </el-form-item>
                </el-col>

                <el-col
                    :span="24">
                    <el-form-item
                        label="Description">
                        <el-input
                            type="textarea"
                            :rows="2"
                            placeholder="Please input.."
                            v-model="form.description">
                        </el-input>
                    </el-form-item>
                </el-col>
            </el-row>

        </el-form>

        <div class="d-flex">
            <div class="ml-auto">
                <el-button
                    @click="toggleForm">
                    zxczxc
                </el-button>
            </div>
        </div>
    </el-card>
</template>

<script>
export default {
    props: ['formTitle'],
    data() {
        return {
            form: {
                name: 'Louise',
                description: ''
            }
        }
    },

    methods: {
        toggleForm() {
            this.$emit('clicked', this.form)
        }
    }
}
</script>

<style>

</style>
