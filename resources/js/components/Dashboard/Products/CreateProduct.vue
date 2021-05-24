<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">افزودن محصول جدید</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('title')}">
                    <label class="font-15 bold-600 mb-2" for="title">عنوان محصول</label>
                    <input type="text" v-model="fields.title" id="title" name="title" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('title')">{{ errors.get('title')}}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('key')}">
                    <label class="font-15 bold-600 mb-2" for="key">کد محصول</label>
                    <input type="text" v-model="fields.key" id="key" name="key" class="form-control ltr" placeholder="" min="1">
                    <span class="error-text" v-if="errors.has('key')">{{ errors.get('key')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2">دسته بندی های محصول</label>
                    <treeselect dir="rtl"
                        :multiple="true"
                        :options="categories"
                        :flat="true"
                        placeholder="دسته بندی محصول"
                        v-model="fields.categories"
                    />
                    <span class="error-text" v-if="errors.has('categories')">{{ errors.get('categories')}}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('price')}">
                    <label class="font-15 bold-600 mb-2" for="price">قیمت</label>
                    <input type="number" v-model="fields.price" id="price" name="price" class="form-control ltr" placeholder="" min="0">
                    <span class="error-text" v-if="errors.has('price')">{{ errors.get('price')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('marketer_percent')}">
                    <label class="font-15 bold-600 mb-2" for="price">درصد بازاریاب</label>
                    <input type="number" v-model="fields.marketer_percent" id="marketer_percent" name="marketer_percent" class="form-control ltr" placeholder="" min="5" max="60">
                    <span class="error-text" v-if="errors.has('marketer_percent')">{{ errors.get('marketer_percent')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('creator_id')}">
                    <label for="user" class="font-15 bold-600 mb-2">اضافه کننده ی محصول</label>
                    <select id="user" name="user" v-model="fields.creator" class="form-control">
                        <option v-for="user in users" :key="user.id+user.type" :value="user" :selected="user.id ==fields.creator_id && user.type==fields.creator_type">{{user.name?user.name:user.mobile}}</option>
                    </select>
                    <span class="error-text" v-if="errors.has('creator_id')">{{ errors.get('creator_id')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('has_discount')}">
                    <label class="font-15 bold-600 mb-2" for="has_discount">فروش ویژه</label>
                    <select v-model="fields.has_discount" id="has_discount" name="type" class="form-control">
                        <option value="0">ندارد</option>
                        <option value="1">دارد</option>
                    </select>
                    <span class="error-text" v-if="errors.has('has_discount')">{{ errors.get('has_discount')}}</span>
                </div>
            </div>
            <div class="col-md-3" v-if="fields.has_discount == 1">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('discounted_price')}">
                    <label class="font-15 bold-600 mb-2" for="price">قیمت فروش ویژه</label>
                    <input type="number" v-model="fields.discounted_price" id="discounted_price" name="discounted_price" class="form-control ltr" placeholder="" min="0">
                    <span class="error-text" v-if="errors.has('discounted_price')">{{ errors.get('discounted_price')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 clearfix">
                <label for="tags">برچسب ها:</label>
                <!-- <msh-select :options="tags" ref="tags" handler="change" @change="updateTag"></msh-select> -->
                <!-- <Select2
                    v-model="fields.tags"
                    :options="fields.tags"
                    :settings="{ multiple: true,tags: true }"
                    id="tags" name="tags" class="form-control"/> -->
                <mtTagCompunnent
                    v-model="fields.tags"
                    id="tags" name="tags" class="form-control"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-12" :class="{'has-error' : errors.has('description')}">
                <label class="font-15 bold-600 mb-2" for="description">متن کامل توضیحات</label>
                <editor v-model="fields.description" tinymce-script-src="/libraries/tinymce/tinymce.min.js" id="description"
                        :init="{
                    emoticons_database_url:
                        '/libraries/tinymce/plugins/emoticons/js/emojis.min.js',
                    skin_url: '/libraries/tinymce/skins/ui/oxide',
                    base_url: '/libraries/tinymce/',
                    height: 500,
                    directionality: 'rtl',
                    plugins: [
                        'lists advlist',
                        'image imagetools',
                        'link autolink',
                        'table',
                        'charmap',
                        'searchreplace visualblocks code fullscreen',
                        'print preview anchor insertdatetime media',
                        'help codesample hr pagebreak nonbreaking toc textpattern noneditable ',
                        'importcss',
                        'directionality',
                        'visualchars',
                        'emoticons',
                        'autosave'
                    ],
                    toolbar:
                        'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
                    branding: false,
                    menubar: true,
                    images_upload_handler: (blobInfo, success, failure) => {
                        const img = 'data:image/jpeg;base64,' + blobInfo.base64();
                        success(img);
                    }
                }"></editor>
                <span class="error-text" v-if="errors.has('description')">{{ errors.get('description')}}</span>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12">
                <label class="font-15 bold-600 mb-2">تصاویر محصول: </label>
                <uploader ref="uploader" size="1000000"
                :upload_path="base_url + '/upload/product_image'"
                :images="[]"
                :delete_path="base_url + ''"
                @filesUpdated="filesUpdated"
                v-model="fields.main_photo"></uploader>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('type')}">
                    <label class="font-15 bold-600 mb-2" for="type">نوع محصول</label>
                    <select v-model="fields.type" id="type" name="type" class="form-control">
                        <option value="physical">محصول فیزیکی</option>
                        <option value="file">فایل</option>
                    </select>
                    <span class="error-text" v-if="errors.has('type')">{{ errors.get('type')}}</span>
                </div>
            </div>
            <div class="col-md-3" v-if="fields.type === 'physical'">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('weight')}">
                    <label class="font-15 bold-600 mb-2" for="price">وزن محصول بر حسب گرم</label>
                    <input type="number" v-model="fields.weight" id="weight" name="weight" class="form-control ltr" placeholder="" min="0">
                    <span class="error-text" v-if="errors.has('weight')">{{ errors.get('weight')}}</span>
                </div>
            </div>
            <div class="col-md-3" v-if="fields.type === 'physical'">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('total')}">
                    <label class="font-15 bold-600 mb-2" for="total">موجودی</label>
                    <input type="number" v-model="fields.total" id="total" name="total" class="form-control ltr" placeholder="" min="0">
                    <span class="error-text" v-if="errors.has('total')">{{ errors.get('total')}}</span>
                </div>
            </div>
            <div class="col-12 file-info-box" v-if="fields.type === 'file'">
                <input type="file" ref="file" name="file_input" id="file_input" class="file_input" @change="handleFileUpload()">
                <div class="upload" v-if="!file">
                    <div class="text-center p-5">
                        <label for="file_input" class="btn btn-lg btn-primary">انتخاب فایل</label>
                    </div>
                </div>
                <table class="lian-table table table-hover table-borderless border-0"  v-if="file">
                    <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <!-- <th class="text-center">پیش نمایش</th> -->
                        <th class="text-center">نام فایل</th>
                        <th class="text-center">حجم</th>
                        <th class="text-center">وضعیت</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td  class="text-center">1</td>
                        <!-- <td class="text-center">
                            <img v-if="file.thumb" :src="file.thumb" width="40" height="auto" />
                            <span v-else>بدون تصویر</span>
                        </td> -->
                        <td class="text-center" dir="ltr">
                            <div class="filename">
                                <a v-if="file.download_path != undefined" target="_blank" :href="file.download_path">{{file.name}}</a>
                                <a v-else-if="file.url != undefined" target="_blank" :href="file.url">{{file.name}}</a>
                                <span v-else>{{file.name}}</span>
                            </div>
                        </td>
                        <td class="text-center">{{file.size | prettyBytes }}</td>
                        <td class="text-center" v-if="errors.has('file')">{{errors.get('file')}}</td>
                        <td class="text-center" v-else-if="this.fields.file">موفق</td>
                        <td class="text-center" v-else-if="upload_progress">فعال</td>
                        <td class="text-center" v-else></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a v-if="file && !fields.file" v-show="!upload_progress"
                                   class="btn btn-success btn-sm text-white" @click="uploadFile()">آپلود</a>
                                <a v-if="file" v-show="!upload_progress" @click="removeFile()"
                                   :class="{'btn-success': this.fields.file}"
                                   class="btn btn-secondary btn-sm text-white">
                                    حذف فایل
                                </a>
                            </div>
                            <!-- <span v-if="upload_progress">لطفا منتظر بمانید</span> -->
                            <div class="progress" v-if="upload_progress">
                                <div :class="{'progress-bar': true, 'progress-bar-striped': true, 'bg-danger': file.error, 'progress-bar-animated': file.active}" role="progressbar" :style="{width: progress_bar_value + '%'}">{{progress_bar_value}}%</div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('show_status')}">
                    <label class="font-15 bold-600 mb-2" for="show_status">وضعیت نمایش</label>
                    <select v-model="fields.show_status" id="show_status" name="type" class="form-control">
                        <option value="0">عدم نمایش</option>
                        <option value="1">نمایش</option>
                    </select>
                    <span class="error-text" v-if="errors.has('show_status')">{{ errors.get('show_status')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('comment_status')}">
                    <label class="font-15 bold-600 mb-2" for="comment_status">وضعیت نظرات</label>
                    <select v-model="fields.comment_status" id="comment_status" name="type" class="form-control">
                        <option value="0">عدم نمایش</option>
                        <option value="1">نمایش</option>
                    </select>
                    <span class="error-text" v-if="errors.has('comment_status')">{{ errors.get('comment_status')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="saveProduct()">ذخیره</button>
                <router-link :to="{name: 'product.list'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
                    <span>بازگشت</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
    import Notifications from "../../elements/Notifications";
    import Editor from '@tinymce/tinymce-vue';
    import Errors from "../../../includes/Errors";
    import Treeselect from '@riophae/vue-treeselect';
    import '@riophae/vue-treeselect/dist/vue-treeselect.css';
    import Uploader from "../../elements/Files/UploaderWithMainPhoto";
    // import MshSelect from "../../elements/mshSelect";
    import Select2 from 'v-select2-component';
    import mtTagCompunnent from "../../elements/mtTagCompunnent";

    export default {
        name: "CreateProduct",
        components : {
            'editor': Editor,
            'Notifications': Notifications,
            'treeselect' : Treeselect,
            'Uploader': Uploader,
            // 'MshSelect' : MshSelect,
            'Select2': Select2,
            'mtTagCompunnent': mtTagCompunnent,
        },
        data() {
            return {
                'fields': {
                    'id': '',
                    'title': '',
                    'creator_id': '',
                    'creator_type': '',
                    'marketer_percent': 0,
                    'price': '',
                    'has_discount': 0,
                    'discounted_price': 0,
                    'key': '',
                    'briefly': '',
                    'description': '',
                    'type': 'file',
                    'order': '',
                    'status': '',
                    'history': '',
                    'categories': [],
                    'file': null,
                    'weight': 0,
                    'total': 0,
                    'images': [],
                    'tags': [],
                    'show_status': 1,
                    'comment_status': 1,
                    'creator':null,
                    'main_photo':null,
                },
                'defaultFieldsValue': {
                    'id': '',
                    'title': '',
                    'creator_id': '',
                    'creator_type': '',
                    'marketer_percent': 0,
                    'price': '',
                    'has_discount': 0,
                    'discounted_price': 0,
                    'key': '',
                    'briefly': '',
                    'description': '',
                    'type': 'file',
                    'order': '',
                    'status': '',
                    'history': '',
                    'categories': [],
                    'file': null,
                    'weight': 0,
                    'images': [],
                    'tags': [],
                    'show_status': 1,
                    'comment_status': 1,
                    'creator':null,
                    'main_photo':null,
                },
                'file': null,
                'in_progress': false,
                'upload_progress': false,
                'progress_bar_value': 0,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/product',
                'mainCategories': [],
                'categories': [],
                'tags': [],
                'users': []
            };
        },
        methods : {
            filesUpdated(images) {
                this.fields.images = [];
                for (let i = 0; i < images.length; i++) {
                    if (images[i].file_id > 0) {
                        this.fields.images.push(images[i].file_id);
                    }
                }
            },
            handleFileUpload() {
                let uploadedFiles = null;

                if (this.$refs.file.files.length > 0) {
                    uploadedFiles = this.$refs.file.files[0]
                } else {
                    uploadedFiles = null;
                }

                this.file = uploadedFiles;
            },
            removeFile() {
                this.file = null;
                this.fields.file = null;
                this.errors.clear('file');
            },
            uploadFile() {
                this.progress_bar_value = 1;
                this.errors.clear('file');
                this.upload_progress = true;
                this.fields.file = null;
                let formData = new FormData();
                let _this = this;
                formData.append('file', this.file);
                /*
                  Make the request to the POST /select-files URL
                */
                axios.post(this.base_url + '/upload/product_file',
                    formData,
                    {
                        timeout: 500000,
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function(progressEvent) {
                           this.progress_bar_value = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        }.bind(this)
                    }
                ).then(response => {
                    if (response.data.returnCode === 0)
                    {
                        this.file = response.data.data;
                        this.fields.file = response.data.data.file_id;
                        this.progress_bar_value = 100;
                    }
                    else
                    {
                        if (Object.keys(response.data.data.errors).length)
                        {
                            this.errors.record(response.data.data.errors);
                        }
                        else
                        {
                            this.errors.record({'general': response.data.message});
                        }
                        this.progress_bar_value = 100;
                    }
                    this.upload_progress = false;
                })
                .catch(error => {
                    this.fields.file = null;
                    this.errors.handle(this, error);
                    this.upload_progress = false;
                    this.progress_bar_value = 100;
                });
            },
            getCategories() {
                window.axios.post(this.base_url + '/get-categories', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.categories = response.data.data.records;
                        }
                        else
                        {
                            this.categories = [];
                            if (Object.keys(response.data.data.errors).length)
                            {
                                this.errors.record(response.data.data.errors);
                            }
                            else
                            {
                                this.errors.record({'general': response.data.message});
                            }
                        }

                        this.$store.state.is_route_loading = false;
                    })
                    .catch(error => {
                        this.errors.handle(this, error);
                    });
            },
            getTags() {
                window.axios.post(this.base_url + '/get-tags', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.tags = response.data.data.records;
                        }
                        else
                        {
                            this.tags = {};
                            if (Object.keys(response.data.data.errors).length)
                            {
                                this.errors.record(response.data.data.errors);
                            }
                            else
                            {
                                this.errors.record({'general': response.data.message});
                            }
                        }

                        this.$store.state.is_route_loading = false;
                    })
                    .catch(error => {
                        this.errors.handle(this, error);
                    });
            },
            updateTag(values) {
                this.fields.tags = values;
            },
            getUsers() {
                window.axios.post(this.base_url + '/get-users', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.users = response.data.data.records;
                            this.fields.creator = this.$store.state.management.user;
                        }
                        else
                        {
                            this.users = {};
                            if (Object.keys(response.data.data.errors).length)
                            {
                                this.errors.record(response.data.data.errors);
                            }
                            else
                            {
                                this.errors.record({'general': response.data.message});
                            }
                        }

                        this.$store.state.is_route_loading = false;
                    })
                    .catch(error => {
                        this.errors.handle(this, error);
                    });
            },
            saveProduct() {
                this.errors.reset();
                this.message = '';
                let _this = this;
                let data = {};
                Object.keys(this.fields).forEach(function(key) {
                    if (_this.fields[key] !== '' && _this.fields[key] !== 0) {
                        data[key] = _this.fields[key];
                    }
                });
                if(this.fields.creator) {
                    data['creator_id'] = this.fields.creator.id
                }

                window.axios.post(this.base_url + '/create', data)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
                            this.fields = JSON.parse(JSON.stringify(this.defaultFieldsValue));
                            this.$refs.uploader.files = [];
                            // this.file = null;
                            this.$router.push({name: 'product.list'})
                        }
                        else
                        {
                            if (Object.keys(response.data.data.errors).length)
                            {
                                this.errors.record(response.data.data.errors);
                            }
                            else
                            {
                                this.errors.record({'general': response.data.message});
                            }
                        }
                        this.$store.state.is_route_loading = false;
                    })
                    .catch(error => {
                        this.errors.handle(this, error);
                    });
            },
            imagesUpdated() {
                this.$emit('imagesUpdated')
            },
        },
        mounted() {
            this.getCategories();
            // this.getTags();
            this.getUsers();
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
    }
</script>

<style lang="sass">
    .file-input-label
        margin-top: 30px
    .file_input
        opacity: 0
        position: absolute
        top: -10000
    .vue-treeselect
        text-align: right !important
    .vue-treeselect__input
        outline: none !important
        box-shadow: none !important
        border: 1px solid #f6f6f8 !important
        background-color: #f6f6f8 !important
    .vue-treeselect__placeholder
        text-align: right !important
    .vue-treeselect__control
        background-color: #f6f6f8 !important
        border: 1px solid #f6f6f8 !important
    .vue-treeselect--focused
        &:not(.vue-treeselect--open)
            .vue-treeselect__control
                border: 1px solid #f9df95 !important
                box-shadow: none !important
        .vue-treeselect__control
            border: 1px solid #f9df95 !important
            box-shadow: none !important
    .vue-treeselect__menu
        border: 1px solid #f9df95 !important
        border-top: 0px !important
        text-align: right
    .vue-treeselect__multi-value
        margin-bottom: 0 !important
    .vue-treeselect--has-value
        .vue-treeselect__input-container
            padding-top: 0 !important
    .upload
        border: 1px solid #ececec
        border-radius: 5px
        padding: 15px
</style>
