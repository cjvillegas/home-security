<template>
    <div>
        <h1>{{ formTitle }}</h1>
        <div v-loading="loading">
            <el-form
                ref="form"
                :label-position="uploadPosition"
                :model="form"
                :rules="rules">
                <el-row :gutter="20">
                    <el-col
                        :span="12"
                        >
                        <el-form-item
                            label="Stock Code"
                            prop="stock_code"
                            :error="hasError('stock_code')">
                            <el-input
                                v-model="form.stock_code"
                                :disabled="this.type === 'view'"
                                clearable>
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Range"
                            prop="range">
                            <el-input
                                v-model="form.range"
                                :disabled="this.type === 'view'"
                                clearable>
                                </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="24">
                        <el-form-item
                            label="Description"
                            prop="description">
                            <el-input
                                type="textarea"
                                :rows="2"
                                placeholder="Please input.."
                                :disabled="this.type === 'view'"
                                v-model="form.description">
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Colour">
                            <el-input
                                v-model="form.colour"
                                :disabled="this.type === 'view'"
                                clearable>
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Size">
                            <el-input
                                v-model="form.size"
                                :disabled="this.type === 'view'"
                                clearable>
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="BD Fabric ID">
                            <el-input
                                v-model="form.bd_fabric_id"
                                :disabled="this.type === 'view'"
                                clearable>
                            </el-input>
                        </el-form-item>
                    </el-col>
                    <el-col
                        :span="12">
                        <el-form-item
                            label="Weight(kg)"
                            :error="hasError('weight')">
                            <el-input-number
                                v-model="form.weight"
                                :controls="false"
                                :disabled="this.type === 'view'"
                                clearable>
                            </el-input-number>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Material ID"
                            :error="hasError('material_id')">
                            <el-tooltip
                                class="item"
                                effect="dark"
                                content=""
                                :open-delay="500"
                                placement="top">
                                <el-input-number
                                    v-model="form.material_id"
                                    placeholder="This to be used for TS2000 barcode generation"
                                    :controls="false"
                                    :disabled="this.type === 'view'"
                                    clearable>
                                </el-input-number>
                            </el-tooltip>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Length">
                            <el-input
                                v-model="form.length"
                                :disabled="this.type === 'view'"
                                clearable>
                            </el-input>
                        </el-form-item>
                    </el-col>

                    <el-col
                        :span="12">
                        <el-form-item
                            label="Product Picture">
                            <div
                                v-loading="loading">
                                <img
                                    v-if="productImageUrl"
                                    :src="productImageUrl"
                                    class="img-circle rounded-circle">
                                    <el-upload
                                        :disabled="this.type === 'view'"
                                        ref="product"
                                        class="avatar-uploader avatar-round"
                                        name="product"
                                        :auto-upload="false"
                                        :show-file-list="false"
                                        :on-preview="productPreview"
                                        :on-remove="productRemove"
                                        list-type="picture"
                                        action=""
                                        accept="image/*"
                                        :on-change="changeProductPicture">
                                        <el-button
                                            :disabled="this.type == 'view'"
                                            v-if="!productImageUrl"
                                            size="small"
                                            type="primary">
                                            <i class="fa fa-upload"></i>
                                            Upload
                                        </el-button>
                                        <div slot="tip" class="el-upload__tip"></div>
                                    </el-upload>
                            </div>
                            <el-button
                                v-if="productImageUrl"
                                :disabled="this.type === 'view'"
                                type="danger"
                                size="small"
                                @click="removePhoto('product')">
                                Remove Photo
                            </el-button>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-divider></el-divider>
                <el-row :gutter="20" class="box-card">
                    <el-col
                        :span="12">
                        <el-card>
                            <el-form-item
                                label="Main Location">
                                <el-input
                                    v-model="form.main_location"
                                    :disabled="this.type === 'view'">
                                </el-input>
                            </el-form-item>

                            <el-form-item
                                label="Main Location Picture">
                                <img v-if="mainLocationImageUrl" :src="mainLocationImageUrl" class="img-circle rounded-circle">
                                <el-upload
                                    :disabled="this.type === 'view'"
                                    ref="main"
                                    class="upload-demo"
                                    name="main"
                                    list-type="picture"
                                    :auto-upload="false"
                                    :show-file-list="false"
                                    action=""
                                    accept="image/*"
                                    :on-change="changeMainLocationPicture">
                                    <el-button
                                        :disabled="this.type == 'view'"
                                        v-if="!mainLocationImageUrl"
                                        size="small"
                                        type="primary">
                                        <i class="fa fa-upload"></i> Select Image File
                                    </el-button>
                                </el-upload>
                                <el-button
                                    v-if="mainLocationImageUrl"
                                    :disabled="this.type === 'view'"
                                    type="danger"
                                    size="small"
                                    @click="removePhoto('main')">
                                    Remove Photo
                                </el-button>
                            </el-form-item>
                        </el-card>

                    </el-col>


                    <el-col
                        :span="12">
                        <el-card>
                            <el-form-item
                                label="Secondary Location">
                                <el-input
                                    v-model="form.secondary_location"
                                    :disabled="this.type === 'view'">
                                </el-input>
                            </el-form-item>
                            <el-form-item
                                label="Secondary Location Picture">
                                <img v-if="secondaryLocationImageUrl" :src="secondaryLocationImageUrl" class="img-circle rounded-circle">
                                <el-upload
                                    :disabled="this.type === 'view'"
                                    ref="secondary"
                                    class="upload-demo"
                                    name="secondary"
                                    list-type="picture"
                                    :auto-upload="false"
                                    :show-file-list="false"
                                    action=""
                                    accept="image/*"
                                    :on-change="changeSecondaryLocationPicture">
                                    <el-button
                                        :disabled="this.type == 'view'"
                                        v-if="!secondaryLocationImageUrl"
                                        size="small"
                                        type="primary">
                                        <i class="fa fa-upload"></i>
                                        Select Image File
                                    </el-button>
                                </el-upload>
                                <el-button
                                    v-if="secondaryLocationImageUrl"
                                    :disabled="this.type === 'view'"
                                    type="danger"
                                    size="small"
                                    @click="removePhoto('secondary')">
                                    Remove Photo
                                </el-button>
                            </el-form-item>
                        </el-card>
                    </el-col>

                    <el-col
                        :span="12"
                        class="mt-5">
                        <el-card>
                            <el-form-item
                                label="Other Location">
                                <el-input
                                    v-model="form.other_location"
                                    :disabled="this.type === 'view'">
                                </el-input>
                            </el-form-item>
                            <el-form-item
                                label="Other Location Picture">
                                <img v-if="otherLocationImageUrl" :src="otherLocationImageUrl" class="img-circle rounded-circle">
                                <el-upload
                                    :disabled="this.type === 'view'"
                                    ref="other"
                                    class="upload-demo"
                                    name="other"
                                    list-type="picture"
                                    :auto-upload="false"
                                    :show-file-list="false"
                                    action=""
                                    :on-change="changeOtherLocationPicture">
                                    <el-button
                                        :disabled="this.type == 'view'"
                                        v-if="!otherLocationImageUrl"
                                        size="small"
                                        type="primary">
                                        <i class="fa fa-upload"></i>
                                        Upload
                                    </el-button>
                                </el-upload>
                                <el-button
                                    v-if="otherLocationImageUrl"
                                    :disabled="this.type === 'view'"
                                    type="danger"
                                    size="small"
                                    @click="removePhoto('other')">
                                    Remove Photo
                                </el-button>
                            </el-form-item>
                        </el-card>

                    </el-col>

                    <el-col
                        :span="12"
                        class="mt-5">
                        <el-card>
                            <el-form-item
                                label="Alternative Item Code">
                                <el-input
                                    v-model="form.alt_item_code"
                                    :disabled="this.type === 'view'">
                                </el-input>
                            </el-form-item>

                            <el-form-item
                                label="Status">
                                <el-select
                                    v-model="form.status"
                                    :disabled="this.type === 'view'"
                                    placeholder="Status">
                                        <el-option
                                            label="Active"
                                            value=1>
                                        </el-option>

                                        <el-option
                                            label="Inactive"
                                            value=0>
                                        </el-option>
                                </el-select>
                            </el-form-item>
                        </el-card>

                    </el-col>
                </el-row>

            </el-form>
        </div>
        <el-divider></el-divider>
        <div class="d-flex">
            <div v-if="this.type != 'view'">
                <el-button
                    @click="cancelForm()">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                    Cancel
                </el-button>
                <el-button
                    v-if="this.type == 'add'"
                    @click="saveStockItem"
                    type="primary">
                    <i  class="fa fa-floppy-o"></i>
                    Save
                </el-button>
                <el-button
                    v-if="this.type == 'edit'"
                    @click="updateStockItem"
                    type="primary">
                    <i  class="fa fa-floppy-o"></i>
                    Update
                </el-button>
            </div>
        </div>
    </div>
</template>

<script>
import { formHelper } from '../../mixins/formHelper'
export default {
    props:{
        formTitle:'',
        model: {},
        type: {
            type: String,
            default: 'create'
        }

    },
    mixins: [formHelper],
    data() {
        return {
            uploadPosition: 'top',
            loading: false,
            form: {
                id: '',
                stock_code:  '',
                description: '',
                range: '',
                colour: '',
                size: '',
                bd_fabric_id: '',
                weight: undefined,
                material_id: undefined,
                length: '',
                product_picture: '',
                main_location: '',
                secondary_location: '',
                other_location: '',
                alt_item_code: '',
                status: '0'
            },
            productImageUrl: '',
            mainLocationImageUrl: '',
            secondaryLocationImageUrl: '',
            otherLocationImageUrl: '',

            rules: {
                stock_code: {required: true, type: 'string', message: 'Stock Code is required', trigger: ['blur', 'chamge']},
            }
        }
    },

    mounted() {
        this.clearForm()
    },

    methods: {
        toggleForm() {
            this.$emit('clicked', this.form)
        },

        saveStockItem() {
            this.$refs.form.validate(valid => {
                if (valid) {
                    let form = this.setFormData('save')
                    this.loading = true
                    this.$emit('saved', form)
                    this.clearForm()
                    this.loading = false
                }
            })


        },

        updateStockItem() {
            this.$refs.form.validate(valid => {
                if (valid) {
                    let form = this.setFormData('update')
                    let apiUrl = `/admin/in-house/stocks/${this.model.id}/update`
                    this.loading = true
                    axios.post(apiUrl, form)
                    .then( (response) => {
                        this.$notify({
                            title: 'Success!',
                            message: response.data.message,
                            type: 'success'
                        });
                        this.populateForm()
                    })
                    .catch((err) => {
                        if (err.response.status === 422) {
                            this.setErrors(err.response.data.errors)
                        }
                    })
                    .finally( () => {
                        this.loading = false
                    })

                }
            })
        },

        setFormData(type) {
            let form = new FormData()
            form.append('stock_code', (this.form.stock_code ? this.form.stock_code : ''))
            form.append('description', (this.form.description ? this.form.description : ''))
            form.append('range', (this.form.range ? this.form.range : ''))
            form.append('colour', (this.form.colour ? this.form.colour : ''))
            form.append('size', (this.form.size ? this.form.size : ''))
            form.append('bd_fabric_id', (this.form.bd_fabric_id ? this.form.bd_fabric_id : ''))
            form.append('weight', (this.form.weight ? this.form.weight : ''))
            form.append('material_id', (this.form.material_id ? this.form.material_id : ''))
            form.append('length', (this.form.length ? this.form.length : ''))
            form.append('main_location', (this.form.main_location ? this.form.main_location : ''))
            form.append('secondary_location', (this.form.secondary_location ? this.form.secondary_location : ''))
            form.append('other_location', (this.form.other_location ? this.form.other_location : ''))
            form.append('alt_item_code', (this.form.alt_item_code ? this.form.alt_item_code : ''))
            form.append('status', (this.form.status ? this.form.status : ''))
            let productImg = ''
            let mainImg = ''
            let secondaryImg = ''
            let otherImg = ''

            //this is to check if <el-upload> has File in it.
            if(this.$refs.product.uploadFiles.length > 0) {
                productImg = this.$refs.product.uploadFiles[0].raw
            }else {
                productImg = this.form.product_picture
            }
            if(this.$refs.main.uploadFiles.length > 0) {
                mainImg = this.$refs.main.uploadFiles[0].raw
            }else {
                mainImg = this.form.main_location_picture
            }
            if(this.$refs.secondary.uploadFiles.length > 0) {
                secondaryImg = this.$refs.secondary.uploadFiles[0].raw
            }else {
                secondaryImg = this.form.secondary_location_picture
            }
            if(this.$refs.other.uploadFiles.length > 0) {
                otherImg = this.$refs.other.uploadFiles[0].raw
            }else {
                otherImg = this.form.other_location_picture
            }

            form.append('product_picture', (productImg ? productImg : ''))
            form.append('main_location_picture', (mainImg ? mainImg : ''))
            form.append('secondary_location_picture', (secondaryImg ? secondaryImg : ''))
            form.append('other_location_picture', (otherImg ? otherImg : ''))


            return form
        },

        productRemove(file, fileList) {
        },
        productPreview(file) {
        },

        changeProductPicture(file, fileList) {
            this.validatePicture(file, fileList, 'product')
        },

        changeMainLocationPicture(file, fileList) {
            this.validatePicture(file, fileList, 'main')
        },

        changeSecondaryLocationPicture(file, fileList) {
            this.validatePicture(file, fileList, 'secondary')
        },

        changeOtherLocationPicture(file, fileList) {
            this.validatePicture(file, fileList, 'other')
        },

        validatePicture(file, fileList, value) {
            this.loading = true
            const fileSize = file.size / 1024 / 1024 < 10;
			const fileType = ['image/jpeg', 'image/png', 'image/jpg', 'image/svg', 'image/gif'].includes(file.raw.type)

            if (!fileType) {
                this.$notify.error({
                    title: 'Invalid File',
                    message: 'Not a supported file type (JPEG, JPG, PNG, SVG)'
                })
                this.loading = false
                return
            }

            if (!fileSize) {
                this.$notify.error({
                    title: 'Max Size',
                    message: 'File size should not exceed 10MB'
                });
                this.loading = false
                return
            }

            if( value == 'product' ) this.productImageUrl = URL.createObjectURL(file.raw)
            else if( value == 'main' ) this.mainLocationImageUrl = URL.createObjectURL(file.raw)
            else if( value == 'secondary' ) this.secondaryLocationImageUrl = URL.createObjectURL(file.raw)
            else if( value == 'other' ) this.otherLocationImageUrl = URL.createObjectURL(file.raw)

            this.loading = false
        },

        uploadMainLocationPicture(value) {
            let form = new FormData()
            let mainFile = document.getElementsByName("main")
            let mainImg = mainFile[0].files[0]
            form.append('main_location_picture', (mainImg ? mainImg : ''))

            let apiUrl = `/admin/in-house/stocks/${this.model.id}/changePicture`
            this.loading = true
            axios.post(apiUrl, form)
            .then( (response) => {
                this.$notify({
                    title: 'Success!',
                    message: response.data.message,
                    type: 'success'
                });
                this.form.main_location_picture = response.data.main_location_picture
                this.mainLocationImageUrl = response.data.main_location_picture
            })
            .catch( (err) => {

            })
            .finally( () => {
                this.loading = false
            })
        },

        uploadSecondaryLocationPicture(value) {
            let form = new FormData()
            let secondaryFile = document.getElementsByName("secondary")
            let secondaryImg = secondaryFile[0].files[0]
            form.append('secondary_location_picture', (secondaryImg ? secondaryImg : ''))

            let apiUrl = `/admin/in-house/stocks/${this.model.id}/changePicture`
            this.loading = true
            axios.post(apiUrl, form)
            .then( (response) => {
                this.$notify({
                    title: 'Success!',
                    message: response.data.message,
                    type: 'success'
                });
                this.form.secondary_location_picture = response.data.secondary_location_picture
                this.secondaryLocationImageUrl = response.data.secondary_location_picture
            })
            .catch( (err) => {

            })
            .finally( () => {
                this.loading = false
            })
        },

        uploadOtherLocationPicture(value) {
            let form = new FormData()
            let otherFile = document.getElementsByName("other")
            let otherImg = otherFile[0].files[0]
            form.append('other_location_picture', (otherImg ? otherImg : ''))

            let apiUrl = `/admin/in-house/stocks/${this.model.id}/changePicture`
            this.loading = true
            axios.post(apiUrl, form)
            .then( (response) => {
                this.$notify({
                    title: 'Success!',
                    message: response.data.message,
                    type: 'success'
                });
                this.form.other_location_picture = response.data.other_location_picture
                this.otherLocationImageUrl = response.data.other_location_picture
            })
            .catch( (err) => {

            })
            .finally( () => {
                this.loading = false
            })
        },

        removePhoto(value) {
             this.$confirm('You are about to remove this Image. Continue?', {
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                type: 'info'
            }).then( () => {
                if(value == 'product') {
                this.$refs.product.clearFiles()
                this.form.product_picture = null
                this.productImageUrl = null
                }
                else if(value == 'main') {
                    this.$refs.main.clearFiles()
                    this.form.main_location_picture = null
                    this.mainLocationImageUrl = null
                }
                else if(value == 'secondary') {
                    this.$refs.secondary.clearFiles()
                    this.form.secondary_location_picture = null
                    this.secondaryLocationImageUrl = null
                }
                else if(value == 'other') {
                    this.$refs.other.clearFiles()
                    this.form.other_location_picture = null
                    this.otherLocationImageUrl = null
                }
            })

        },

        populateForm() {
            let apiUrl = `/admin/in-house/stocks/${this.model.id}/show`
            this.loading = true
            axios.get(apiUrl).then( (response) => {
                this.form = response.data.stockItem
                this.form.status = response.data.stockItem.status == 'Active' ? '1' : '0'
                if(this.form.product_picture != null && this.form.product_picture != '') {
                    this.productImageUrl = '/storage/' + this.form.product_picture
                }
                if(this.form.main_location_picture != null && this.form.main_location_picture != '') {
                    this.mainLocationImageUrl = '/storage/' + this.form.main_location_picture
                }
                if(this.form.secondary_location_picture != null && this.form.secondary_location_picture != '') {
                    this.secondaryLocationImageUrl = '/storage/' + this.form.secondary_location_picture
                }
                if(this.form.other_location_picture != null && this.form.other_location_picture != '') {
                    this.otherLocationImageUrl = '/storage/' + this.form.other_location_picture
                }
                console.log(this.form)
                this.loading = false
            })
        },

        cancelForm() {
            this.$emit('toggle', 'back')
        },

        clearForm() {
            this.form = {
                stock_code: '',
                description: '',
                range: '',
                colour: '',
                size: '',
                bd_fabric_id: '',
                weight: undefined,
                material_id: undefined,
                length: '',
                product_picture: '',
                main_location: '',
                secondary_location: '',
                other_location: '',
                alt_item_code: '',
                status: '0' //Default is inactive
            }
            this.productImageUrl = ''
            this.mainLocationImageUrl =''
            this.secondaryLocationImageUrl =''
            this.otherLocationImageUrl =''
        }
    },

    computed: {
        hasModel() {
            return this.model && this.model.id
        },
    },

    watch: {
        model: {
            handler() {
                if (this.hasModel) {
                    this.populateForm()
                }
            },
            deep: true,
            immediate: true
        }
    }

}
</script>

<style scoped>
    .img-circle {
        height: 100px !important;
        width: 100px !important;
        margin-bottom: 2px !important;
    }
    .el-card {
        height: 320px !important;
    }

    .el-select {
        width: 100% !important;
    }

    .el-input-number {
        width: 100% !important;
    }
</style>
