<template>
    <el-dialog
        :visible.sync="showDialog"
        :title="title"
        @close="closeManager"
        :width="width">
        <div
            v-if="columnManager.columns.length <= minFieldSelected"
            class="mb-3">
            <el-alert
                :title="`You cannot unselect fields. Required min selected fields are ${minFieldSelected}.`"
                type="info"
                :closable="false"
                show-icon>
            </el-alert>
        </div>
        <div v-loading="loading">
            <el-checkbox
                @change="checkAll"
                v-model="checked"
                :indeterminate="isIndeterminate">
                Select All
            </el-checkbox>

            <el-checkbox-group
                v-model="columnManager.columns"
                :min="minFieldSelected">
                <div class="row">
                    <div
                        v-for="col in columns"
                        :key="col.prop"
                        class="col-md-3">
                        <el-checkbox :label="col.prop">{{ col.label | ucWords }}</el-checkbox>
                    </div>
                </div>
            </el-checkbox-group>
        </div>

        <span
            slot="footer"
            class="dialog-footer">
		    <el-button
                @click="closeManager"
                :disabled="loading">
		    	Close
		    </el-button>
		    <el-button
                @click="saveColumnConfig"
                :disabled="loading || disableSave"
                type="primary"
                class="btn-primary">
		    	Save Config
		    </el-button>
		</span>
    </el-dialog>
</template>

<script>
    import cloneDeep from "lodash/cloneDeep"
    import {dialog} from "../../../mixins/dialog"

    export default {
        name: "GlobalColumnManager",

        mixins: [dialog],

        props: {
            title: {
                required: false,
                type: String,
                default: 'Column Manager'
            },

            width: {
                required: false,
                type: String,
                default: '40%'
            },

            columns: {
                required: true,
                type: Array
            },

            type: {
                required: true,
                type: Number
            },

            defaultColumns: {
                type: Array,
                required: false,
                default: _ => []
            },

            minFieldSelected: {
                type: Number,
                required: false,
                default: 5
            }
        },

        data() {
            return {
                loading: false,
                checked: false,
                columnManager: {
                    id: null,
                    user_id: null,
                    type: this.type,
                    columns: []
                },
                oldSelectedColumns: [],
                defaultCols: this.defaultColumns
            }
        },

        computed: {
            isIndeterminate() {
                let len = this.columnManager.columns.length
                let allColLen = this.columns.length

                return len > 0 && len < allColLen
            },

            disableSave() {
                if (!this.columnManager.id ) {
                    return false
                }

                return JSON.stringify(this.oldSelectedColumns) === JSON.stringify(this.columnManager.columns)
            }
        },

        created() {
            // if no default columns passed get if from the passed columns
            if (!this.defaultColumns.length) {
                this.getDefaultColumns()
            }

            this.getColumnManagerByType()
        },

        methods: {
            saveColumnConfig() {
                if (this.columnManager.columns.length <= 0) {
                    this.$notify.error({
                        title: this.title,
                        message: 'You cannot save an empty table column configuration.'
                    })

                    return
                }

                if (this.columnManager.id) {
                    this.updateColumnConfig()

                    return
                }

                this.createColumnConfig()
            },

            createColumnConfig() {
                this.loading = true

                this.$API.TableColumnManager.store(this.columnManager)
                .then(res => {
                    this.columnManager = res.data

                    this.updateParent()
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            updateColumnConfig() {
                this.loading = true

                this.$API.TableColumnManager.update(this.columnManager, this.columnManager.id)
                    .then(res => {
                        this.columnManager = res.data

                        this.updateParent()
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(_ => {
                        this.loading = false
                    })
            },

            getColumnManagerByType() {
                this.loading = true

                this.$API.TableColumnManager.showByType(this.type)
                .then(res => {
                    if (res.data.data) {
                        this.columnManager = res.data.data
                    } else {
                        this.columnManager.columns = this.defaultCols
                    }

                    this.updateParent()
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.loading = false
                })
            },

            checkAll(checked) {
                if (checked) {
                    this.columnManager.columns = this.columns.map(col => col.prop)

                    return
                }

                this.columnManager.columns = cloneDeep(this.defaultCols)
            },

            updateParent() {
                this.oldSelectedColumns = cloneDeep(this.columnManager.columns)

                this.$emit('change', this.columnManager.columns)

                setTimeout(_ => {
                    this.closeManager()
                }, 300)
            },

            getDefaultColumns() {
                this.defaultCols = this.columns
                    .filter((col, index) => index < this.minFieldSelected)
                    .map(col => col.prop)
            },

            closeManager() {
                this.closeModal()

                this.columnManager.columns = cloneDeep(this.oldSelectedColumns)
            },
        },

        watch: {
            'columnManager.columns': {
                handler(cols) {
                    this.checked = cols.length === this.columns.length
                },
                immediate: true
            }
        }
    }
</script>
