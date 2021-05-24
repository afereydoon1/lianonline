<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">ویرایش دیدگاه</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('title')}">
                    <label class="font-15 bold-600 mb-2" for="title">عنوان <span>{{fields.commentable_type == 'App\\Models\\Post' ? 'نوشته' : 'محصول'}}</span></label>
                    <input v-if="fields.commentable" type="text" v-model="fields.commentable.title" id="title" name="title" class="form-control" placeholder="" disabled>
                    <span class="error-text" v-if="errors.has('title')">{{ errors.get('title')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('name')}">
                    <label class="font-15 bold-600 mb-2" for="name">نویسنده</label>
                    <input type="text" v-if="fields.authorable" v-model="fields.authorable.name" id="name" name="name" class="form-control" placeholder="" disabled>
                    <input type="text" v-else v-model="fields.name" id="name" name="name" class="form-control" placeholder="" disabled>
                    <span class="error-text" v-if="errors.has('name')">{{ errors.get('name')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('email')}">
                    <label class="font-15 bold-600 mb-2" for="email">ایمیل</label>
                    <input type="text" v-if="fields.authorable" v-model="fields.authorable.email" id="email" name="email" class="form-control" placeholder="" disabled>
                    <input type="text" v-else v-model="fields.email" id="email" name="email" class="form-control" placeholder="" disabled>
                    <span class="error-text" v-if="errors.has('email')">{{ errors.get('email')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" :class="{'has-error' : errors.has('body')}">
                <label class="font-15 bold-600 mb-2" for="body">متن</label>
                <editor v-model="fields.body" tinymce-script-src="/libraries/tinymce/tinymce.min.js" id="body"
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
                <span class="error-text" v-if="errors.has('body')">{{ errors.get('body')}}</span>
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
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="save()">ذخیره</button>
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
        name: "EditComment",
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
                    'text': '',
                    'commentable':{
                        'title':null,
                    },
                    'authorable':null,
                    'show_status': 1,
                    'name':'',
                    'commentable_type':'',
                    'email':'',
                },
                'defaultFieldsValue': {
                    'id': '',
                    'text': '',
                    'commentable':{
                        'title':null,
                    },
                    'authorable':null,
                    'show_status': 1,
                    'name':'',
                    'commentable_type':'',
                    'email':'',
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'assortments': [],
                'tags': [],
                'base_url': '/management/api/comments',
                'users':[],

            };
        },
        watch: {
        },
        methods : {
            getInfo() {
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
            },
            save() {
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
            this.getInfo();
        },
    }
</script>

<style lang="sass">

</style>
