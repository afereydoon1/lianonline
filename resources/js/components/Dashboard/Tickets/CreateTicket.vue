<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">ثبت تیکت جدید</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('title')}">
                    <label class="font-15 bold-600 mb-2" for="title">عنوان</label>
                    <input type="text" v-model="fields.title" id="title" name="title" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('title')">{{ errors.get('title')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('price')}">
                    <label class="font-15 bold-600 mb-2" for="receiver_id">کاربر</label>
                    <!--
                    <input type="text" v-model="fields.price" id="price" name="price" class="form-control ltr" placeholder="" disabled>
                    <span class="error-text" v-if="errors.has('price')">{{ errors.get('price')}}</span>
                    -->
                    <select v-model="fields.receiver_id" id="receiver_id" name="receiver_id" class="form-control">
                        <option v-for="user in users" :key="user.id" :value="user.id">{{user.name?user.name:user.mobile}}</option>
                    </select>
                    <span class="error-text" v-if="errors.has('receiver_id')">{{ errors.get('receiver_id')}}</span>
                </div>
            </div>
        <!--<div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('price')}">
                    <label class="font-15 bold-600 mb-2" for="price">زمان</label>
                    <input type="text" v-model="fields.price" id="price" name="price" class="form-control ltr" placeholder="" disabled>
                    <span class="error-text" v-if="errors.has('price')">{{ errors.get('price')}}</span>
                </div>
            </div>-->
           <div class="col-md-4">
                <!--<div class="form-group mb-4" :class="{'has-error' : errors.has('price')}">
                    <label class="font-15 bold-600 mb-2" for="price">وضعیت</label>
                    <select v-model="fields.has_discount" id="has_discount" name="type" class="form-control">
                        <option value="0">در انتظار</option>
                        <option value="1">پاسخ داده شده</option>
                        <option value="2">بسته شده</option>
                    </select>
                    <span class="error-text" v-if="errors.has('has_discount')">{{ errors.get('has_discount')}}</span>
                </div>-->
                <div class="form-group mb-4" :class="{'has-error' : errors.has('type')}">
                    <label class="font-15 bold-600 mb-2" for="type">نوع</label>
                    <select v-model="fields.type" id="type" name="type" class="form-control">
                        <option value="support">پشتیبانی</option>
                        <option value="report_abuse">گزارش تخلف</option>
                        <option value="copy_right">کپی رایت</option>
                    </select>
                    <span class="error-text" v-if="errors.has('type')">{{ errors.get('type')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" :class="{'has-error' : errors.has('text')}">
                <label class="font-15 bold-600 mb-2" for="text">متن تیکت</label>
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
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="save()">ذخیره</button>
                <router-link :to="{name: 'tickets.list'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
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

    export default {
        name: "CreateTicket",
        components : {
            'editor': Editor,
            'Notifications': Notifications,
        },
        data() {
            return {
                'fields': {
                    'title': '',
                    'users': [],
                    'receiver_id': '',
                    'receiver_type': 'User',
                    'type': 'support',
                    'text':'',
                },
                'defaultFieldsValue': {
                    'title': '',
                    'users': [],
                    'receiver_id': '',
                    'receiver_type': 'User',
                    'type': 'support',
                    'text':'',
                },
                'file': null,
                'in_progress': false,
                'upload_progress': false,
                'progress_bar_value': 0,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api',
                'users': []
            };
        },
        methods : {
            getUsers() {
                window.axios.post(this.base_url + '/getAllUsers', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.users = response.data.data.records;
                            this.fields.text = ''
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
            save() {
                this.errors.reset();
                this.message = '';
                let _this = this;
                let data = {};
                window.axios.post(this.base_url + '/create-ticket', this.fields)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
                            this.fields = JSON.parse(JSON.stringify(this.defaultFieldsValue));
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
            this.getUsers();
        },
        filters: {
            fromNow: function (date) {
                    moment.locale('fa');
                    return moment(date).fromNow(); ;
                }
        }

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
