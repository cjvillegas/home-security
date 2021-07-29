<template>
    <el-card>
        <h1>{{ formTitle }}</h1>
        <el-form>
            <el-form-item>
                <el-input></el-input>
            </el-form-item>
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
                name: 'Louise'
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
