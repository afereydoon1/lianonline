<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">ویرایش نوشته</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('title')}">
                    <label class="font-15 bold-600 mb-2" for="title">عنوان نوشته</label>
                    <input type="text" v-model="fields.title" id="title" name="title" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('title')">{{ errors.get('title')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2">دسته بندی های نوشته</label>
                    <treeselect dir="rtl"
                                :multiple="true"
                                :options="assortments"
                                :flat="true"
                                placeholder="دسته بندی نوشته"
                                v-model="fields.assortments"
                    />
                    <span class="error-text" v-if="errors.has('assortments')">{{ errors.get('assortments')}}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('creator_id')}">
                    <label for="user" class="font-15 bold-600 mb-2">نویسنده</label>
                    <select id="user" name="user" v-model="fields.creator" class="form-control">
                        <option v-for="user in users" :key="user.id+user.type" :value="user" :selected="user.id ==fields.creator_id && user.type==fields.creator_type">{{user.name?user.name:user.mobile}}</option>
                    </select>

                    <span class="error-text" v-if="errors.has('creator_id')">{{ errors.get('creator_id')}}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 clearfix">
                <label for="tags">برچسب ها:</label>
                <!-- <msh-select :options="tags" ref="tags" handler="change" @change="updateTag"></msh-select> -->
                <!-- <Select2
                    ref="mytags"
                    v-model="fields.tags"
                    :options="fields.tags"
                    :settings="{ multiple: true,tags: true, closeOnSelect: false ,minimumResultsForSearch:0 }"
                    @change="myChangeEvent($event)"
                    id="tags" name="tags" class="form-control"/> -->
                <mtTagCompunnent
                v-model="fields.tags"
                id="tags" name="tags" class="form-control"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-12" :class="{'has-error' : errors.has('text')}">
                <label class="font-15 bold-600 mb-2" for="text">متن کامل</label>
                <editor v-model="fields.text" tinymce-script-src="/libraries/tinymce/tinymce.min.js" id="text"
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
                <span class="error-text" v-if="errors.has('text')">{{ errors.get('text')}}</span>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12">
                <label class="font-15 bold-600 mb-2">تصویر نوشته: </label>
                <uploader ref="uploader" size="1000000"
                :upload_path="base_url + '/upload/image'"
                :images="fields.image?[fields.image]:[]"
                :delete_path="base_url + ''"
                @filesUpdated="filesUpdated"></uploader>
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
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="savePost()">ذخیره</button>
                <router-link :to="{name: 'posts.list'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
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
    import Treeselect from "@riophae/vue-treeselect";
    // import MshSelect from "../../elements/mshSelect";
    import Select2 from 'v-select2-component';
    import Uploader from "../../elements/Files/Uploader";

    import mtTagCompunnent from "../../elements/mtTagCompunnent";

    export default {
        name: "CreatePost",
        components : {
            'editor': Editor,
            'Notifications': Notifications,
            'treeselect' : Treeselect,
            // 'MshSelect' : MshSelect,
            'Select2': Select2,
            'Uploader': Uploader,
            'mtTagCompunnent': mtTagCompunnent,
        },
        data() {
            return {
                'fields': {
                    'id': '',
                    'title': '',
                    'text': '',
                    'assortments': [],
                    'tags': [],
                    'image': null,
                    'image_id': '',
                    'show_status': 1,
                    'comment_status': 1,
                    'creator':null
                },
                'defaultFieldsValue': {
                    'id': '',
                    'title': '',
                    'text': '',
                    'assortments': [],
                    'tags': [],
                    'image': null,
                    'image_id': '',
                    'show_status': 1,
                    'comment_status': 1,
                    'creator':null
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'assortments': [],
                'tags': [],
                'base_url': '/management/api/post',
                'users':[],

            };
        },
        watch: {
            'fields.tags':function() {
                for (let i = 0; i < this.tags.length; i++) {
                    for (let j = 0; j < this.fields.tags.length; j++) {
                        if (this.fields.tags[j].id === this.tags[i].value) {
                            this.tags[i].selected = true;
                        }
                    }
                }
                // this.$refs.tags.items = this.tags;
            }
        },
        methods : {
            filesUpdated(images) {
                this.fields.image_id = '';
                if(images.length > 0) {
                    if(images[images.length-1].file_id > 0) {
                        this.fields.image = images[images.length-1];
                        this.fields.image_id = this.fields.image.file_id;
                    }
                }
            },
            getPostInfo() {
                window.axios.post(this.base_url + '/show/' + this.$route.params.id, {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.fields = response.data.data.record;
                            if(response.data.data.record.image) {
                                this.fields.image_id = response.data.data.record.image.id;
                            }

                            let assortments = [];
                            for (let i = 0; i < response.data.data.record.assortments.length; i++) {
                                assortments.push(response.data.data.record.assortments[i].id);
                            }
                            this.fields.assortments = assortments;
                            this.fields.tags = this.fields.tags.map(item => item.name)
                        } else
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
            },getUsers() {
                window.axios.post(this.base_url + '/get-users', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.users = response.data.data.records;
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
            getAssortments() {
                window.axios.post(this.base_url + '/get-assortments', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.assortments = response.data.data.records;
                        }
                        else
                        {
                            this.assortments = [];
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
            savePost() {
                this.errors.clear('general');
                this.message = '';

                window.axios.post(this.base_url + '/edit/' + this.fields.id, this.fields)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
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
        },
        mounted() {
            this.getAssortments();
            this.getUsers();
            // this.getTags();
            this.getPostInfo();
        },
    }
</script>

<style lang="sass">

</style>
