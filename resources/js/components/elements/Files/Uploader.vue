<template>
    <div class="upload">
        <div class="table-responsive">
            <div class="upload-box"  v-if="!files.length">
                <div class="text-center p-5">
                    <h4>فایل های خود را جهت آپلود به اینجا بکشید<br/>یا</h4>
                    <label :for="name" class="btn btn-lg btn-primary">انتخاب فایل</label>
                </div>
            </div>
            <table class="lian-table table table-hover table-borderless border-0"  v-if="files.length">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">پیش نمایش</th>
                    <th class="text-center">نام فایل</th>
                    <!-- <th class="text-center">حجم</th>
                    <th class="text-center">سرعت آپلود</th> -->
                    <th class="text-center">وضعیت</th>
                    <th class="text-center">عملیات</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(file, index) in files" :key="file.id">
                    <td  class="text-center">{{index + 1}}</td>
                    <td class="text-center">
                        <img v-if="file.thumb" :src="file.thumb" width="40" height="auto" />
                        <img v-else-if="file.download_path" :src="file.download_path" width="40" height="auto" />
                        <span v-else >بدون تصویر</span>
                    </td>
                    <td class="text-center" dir="ltr">
                        <div class="filename">
                            <a v-if="file.download_path != undefined" target="_blank" :href="file.download_path">{{file.name}}</a>
                            <span v-else>{{file.name}}</span>
                        </div>
                        <div class="progress" v-if="file.download_path == undefined && (file.active || file.progress !== '0.00')">
                            <div :class="{'progress-bar': true, 'progress-bar-striped': true, 'bg-danger': file.error, 'progress-bar-animated': file.active}" role="progressbar" :style="{width: file.progress + '%'}">{{file.progress}}%</div>
                        </div>
                    </td>
                    <!-- <td class="text-center">{{file.size | prettyBytes }}</td>
                    <td class="text-center">{{file.speed | prettyBytes }}</td> -->

                    <td class="text-center" v-if="file.error">{{file.error}}</td>
                    <td class="text-center" v-else-if="file.success">موفق</td>
                    <td class="text-center" v-else-if="file.active">در حال آپلود</td>
                    <td class="text-center" v-else-if="file.download_path != undefined">آپلودشده</td>
                    <td class="text-center" v-else></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <div>
                                <a :class="{disabled: !file.active}" href="#" alt="انصراف" v-show="file.download_path == undefined"
                                   @click.prevent="file.active ? $refs.upload.update(file, {error: 'cancel'}) : false">
                                    <i class="fas fa-ban"></i>
                                </a>
                                <a href="#" v-if="file.active" alt="انصراف"
                                   @click.prevent="$refs.upload.update(file, {active: false})">
                                    <i class="fas fa-eject"></i>
                                </a>
                                <a href="#" alt="تلاش مجدد"
                                   v-else-if="file.error && file.error !== 'compressing' && $refs.upload.features.html5"
                                   @click.prevent="$refs.upload.update(file, {active: true, error: '', progress: '0.00'})">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                                <a :class="{disabled: file.success || file.error === 'compressing' }" href="#" alt="آپلود" v-else v-show="file.download_path == undefined"
                                @click.prevent="file.success || file.error === 'compressing' ? false : $refs.upload.update(file, {active: true})">
                                    <i class="fas fa-upload"></i>
                                </a>
                                <a href="#" @click.prevent="$refs.upload.remove(file);$emit('filesUpdated', files);" alt="حذف">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="example-foorer" v-show="files.length">
            <div class="btn-group" >
                <file-upload
                    class="btn btn-info"
                    :post-action="upload_path"
                    :put-action="upload_path"
                    :custom-action="uploadFile"
                    :extensions="extensions"
                    :accept="accept"
                    :multiple="multiple"
                    :directory="directory"
                    :size="size || 0"
                    :thread="thread < 1 ? 1 : (thread > 5 ? 5 : thread)"
                    :headers="headers"
                    :data="{}"
                    :drop="drop"
                    :drop-directory="dropDirectory"
                    :add-index="addIndex"
                    v-model="files"
                    ref="upload"
                    @input-filter="inputFilter"
                    @input-file="inputFile">
                    <i class="fa fa-plus"></i>
                    انتخاب فایل
                </file-upload>
            </div>
            <button type="button" class="btn btn-success" v-if="!$refs.upload || !$refs.upload.active" @click.prevent="$refs.upload.active = true">
                <i class="fa fa-arrow-up" aria-hidden="true"></i>
                آپلود
            </button>
            <button type="button" class="btn btn-danger"  v-else @click.prevent="$refs.upload.active = false">
                <i class="fa fa-stop" aria-hidden="true"></i>
                انصراف
            </button>
        </div>
    </div>
</template>

<script>
    import FileUpload from 'vue-upload-component';
    import ImageCompressor from 'compressorjs';
    export default {
        name: "Uploader",
        components: {
            FileUpload
        },
        props: [
            'upload_path',
            'delete_path',
            'images'
            // 'file_types',
            // 'extensions'
        ],
        data() {
            return {
                files: [],
                accept: 'image/png,image/gif,image/jpeg,image/webp',
                extensions: 'gif,jpg,jpeg,png,webp',
                // extensions: ['gif', 'jpg', 'jpeg','png', 'webp'],
                // extensions: /\.(gif|jpe?g|png|webp)$/i,
                minSize: 1024,
                size: 1024 * 1024 * 10,
                multiple: true,
                directory: false,
                drop: true,
                dropDirectory: true,
                addIndex: false,
                thread: 3,
                name: 'file',
                // postAction: '/upload/post',
                // putAction: '/upload/put',
                headers: {
                    // 'X-Csrf-Token': 'xxxx',
                    'Authorization': 'Bearer ' + window.localStorage.management_access_token
                },
                data: {},
                autoCompress: 1024 * 1024,
                uploadAuto: false,
                isOption: false,
                addData: {
                    show: false,
                    name: '',
                    type: '',
                    content: '',
                },
                editFile: {
                    show: false,
                    name: '',
                },
            }
        },
        filters: {
            prettyBytes: function (value) {
                if (!value) return '';
                value = parseInt(value, 0);

                if (value > 1073741824) {
                    return Math.floor(value / 1073741824) + ' گیگابایت';
                } else if (value > 1048576) {
                    return Math.floor(value / 1048576) + ' مگابایت';
                } else if (value > 1024) {
                    return Math.floor(value / 1024) + ' کیلوبایت';
                } else {
                    return value + ' بایت';
                }

            }
        },
        watch: {
            'editFile.show'(newValue, oldValue) {
                if (!newValue && oldValue) {
                    this.$refs.upload.update(this.editFile.id, { error: this.editFile.error || '' })
                }
                if (newValue) {
                    this.$nextTick(function () {
                        if (!this.$refs.editImage) {
                            return
                        }
                        let cropper = new Cropper(this.$refs.editImage, {
                            autoCrop: false,
                        })
                        this.editFile = {
                            ...this.editFile,
                            cropper
                        }
                    })
                }
            },
            'addData.show'(show) {
                if (show) {
                    this.addData.name = ''
                    this.addData.type = ''
                    this.addData.content = ''
                }
            },
            'images'(newValue, oldValue){
                if(this.images) {
                    if(Array.isArray(this.images)) {
                        if(this.images.length > 0) {
                            this.files = [...this.images];
                        }
                    }
                }
            }
        },
        methods: {
            async uploadFile (file, component) {
                let formData = new FormData();
                formData.append('file', file.file);
                window.axios.post( this.upload_path,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(response => {
                    if (response.data.returnCode === 0)
                    {
                        for (let i = 0; i < this.files.length; i++) {
                            if (this.files[i].id === file.id) {
                                file.progress=100;
                                file.file_id = response.data.data.file_id;
                                this.files[i].progress = 100;
                                this.files[i].file_id = response.data.data.file_id;
                            }
                        }
                    }
                    else
                    {
                        for (let i = 0; i < this.files.length; i++) {
                            if (this.files[i].id === file.id) {
                                file.progress=0;
                                this.files[i].progress = 0;
                                file.success=false;
                                this.files[i].success = false;
                                file.error=response.data.message;
                                this.files[i].error = response.data.message;
                            }
                        }
                    }
                    this.$store.state.is_route_loading = false;
                    this.$emit('filesUpdated', this.files);
                })
                .catch(error => {
                    for (let i = 0; i < this.files.length; i++) {
                        if (this.files[i].id === file.id) {
                            file.progress=0;
                            this.files[i].progress = 0;
                            file.success=false;
                            this.files[i].success = false;

                            if (typeof error.response.data.data.errors !== typeof undefined) {
                                file.error=error.response.data.data.errors.file[0];
                                this.files[i].error = error.response.data.data.errors.file[0];
                            } else {
                                file.error=error.response.data.message;
                                this.files[i].error = error.response.data.message;
                            }

                        }
                    }
                    this.$emit('filesUpdated', this.files);
                });
            },



            //////////////////////////////

            inputFilter(newFile, oldFile, prevent) {
                if (newFile && !oldFile) {
                    // Before adding a file
                    // 添加文件前
                    // Filter system files or hide files
                    // 过滤系统文件 和隐藏文件
                    if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
                        return prevent()
                    }
                    // Filter php html js file
                    // 过滤 php html js 文件
                    if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
                        return prevent()
                    }
                    // Automatic compression
                    // 自动压缩
                    if (newFile.file && newFile.type.substr(0, 6) === 'image/' && this.autoCompress > 0 && this.autoCompress < newFile.size) {
                        newFile.error = 'compressing'
                        const upl = this.$refs.upload
                        new ImageCompressor(newFile.file,{
                            convertSize: Infinity,
                            maxWidth: 512,
                            maxHeight: 512,
                            success(file) {
                                upl.update(newFile, { error: '', file, size: file.size, type: file.type })
                            },
                            error(err) {
                                upl.update(newFile, { error: err.message || 'compress' })
                            },
                        });
                    }
                }
                if (newFile && (!oldFile || newFile.file !== oldFile.file)) {
                    // Create a blob field
                    // 创建 blob 字段
                    newFile.blob = ''
                    let URL = window.URL || window.webkitURL
                    if (URL && URL.createObjectURL) {
                        newFile.blob = URL.createObjectURL(newFile.file)
                    }
                    // Thumbnails
                    // 缩略图
                    newFile.thumb = ''
                    if (newFile.blob && newFile.type.substr(0, 6) === 'image/') {
                        newFile.thumb = newFile.blob
                    }
                }
            },
            // add, update, remove File Event
            inputFile(newFile, oldFile) {
                if (newFile && oldFile) {
                    // update
                    if (newFile.active && !oldFile.active) {
                        // beforeSend
                        // min size
                        if (newFile.size >= 0 && this.minSize > 0 && newFile.size < this.minSize) {
                            this.$refs.upload.update(newFile, { error: 'size' })
                        }
                    }
                    if (newFile.progress !== oldFile.progress) {
                        // progress
                    }
                    if (newFile.error && !oldFile.error) {
                        // error
                    }
                    if (newFile.success && !oldFile.success) {
                        // success
                    }
                }
                if (!newFile && oldFile) {
                    // remove
                    if (oldFile.success && oldFile.response.id) {
                        // $.ajax({
                        //   type: 'DELETE',
                        //   url: '/upload/delete?id=' + oldFile.response.id,
                        // })
                    }
                }
                // Automatically activate upload
                if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
                    if (this.uploadAuto && !this.$refs.upload.active) {
                        this.$refs.upload.active = true
                    }
                }
            },
            alert(message) {
                alert(message)
            },
            onEditFileShow(file) {
                this.editFile = { ...file, show: true }
                this.$refs.upload.update(file, { error: 'edit' })
            },
            onEditorFile() {
                if (!this.$refs.upload.features.html5) {
                    this.alert('Your browser does not support')
                    this.editFile.show = false
                    return
                }
                let data = {
                    name: this.editFile.name,
                }
                if (this.editFile.cropper) {
                    let binStr = atob(this.editFile.cropper.getCroppedCanvas().toDataURL(this.editFile.type).split(',')[1])
                    let arr = new Uint8Array(binStr.length)
                    for (let i = 0; i < binStr.length; i++) {
                        arr[i] = binStr.charCodeAt(i)
                    }
                    data.file = new File([arr], data.name, { type: this.editFile.type })
                    data.size = data.file.size
                }
                this.$refs.upload.update(this.editFile.id, data)
                this.editFile.error = ''
                this.editFile.show = false
            },
            // add folader
            onAddFolader() {
                if (!this.$refs.upload.features.directory) {
                    this.alert('Your browser does not support')
                    return
                }
                let input = this.$refs.upload.$el.querySelector('input')
                input.directory = true
                input.webkitdirectory = true
                this.directory = true
                input.onclick = null
                input.click()
                input.onclick = (e) => {
                    this.directory = false
                    input.directory = false
                    input.webkitdirectory = false
                }
            },
            onAddData() {
                this.addData.show = false
                if (!this.$refs.upload.features.html5) {
                    this.alert('Your browser does not support')
                    return
                }
                let file = new window.File([this.addData.content], this.addData.name, {
                    type: this.addData.type,
                })
                this.$refs.upload.add(file)
            }

            /////////////////////////////
        }
    }
</script>

<style lang="sass">
.upload
    border: 1px solid #ececec
    border-radius: 5px
    padding: 15px
</style>
