<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">ویرایش تیکت</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2" for="title">عنوان</label>
                    <input type="text" v-model="fields.title" id="title" name="title" class="form-control" placeholder="" disabled>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2" for="username">کاربر</label>
                    <div v-if="fields.creator" class="form-control">{{fields.creator.name == this.$store.state.management.user.name ?  (fields.receiver.name ? fields.receiver.name : fields.receiver.mobile): fields.creator.name}}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2" for="time">زمان</label>
                    <input type="text" v-model="stringTime" id="time" name="time" class="form-control ltr" placeholder="" disabled>
                </div>
            </div>
           <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('status')}">
                    <label class="font-15 bold-600 mb-2" for="status">وضعیت</label>
                    <select v-model="fields.status" id="status" name="status" class="form-control">
                        <option value="new">باز</option>
                        <option value="answered">پاسخ داده شده</option>
                        <option value="processing">در حال انجام</option>
                        <option value="closed">بسته شده</option>
                    </select>
                    <span class="error-text" v-if="errors.has('status')">{{ errors.get('status')}}</span>
                </div>
           </div>
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('type')}">
                    <label class="font-15 bold-600 mb-2" for="type">نوع</label>
                    <select v-model="fields.type" id="type" name="type" class="form-control" disabled>
                        <option value="support">پشتیبانی</option>
                        <option value="report_abuse">گزارش تخلف</option>
                        <option value="copy_right">کپی رایت</option>
                    </select>
                    <span class="error-text" v-if="errors.has('type')">{{ errors.get('type')}}</span>
                </div>
            </div>
        </div>
       <div v-if="fields.ticket_items.length > 0" class="row mx-5">
            <div class="col-12 pt-2" v-for="ticket in fields.ticket_items" :key="ticket.id">
                <div class="row bg-dark rounded-2">
                    <div class="col-6 text-right py-2">
                        <h2 class="h4 text-white">{{ticket.creator?ticket.creator.name:'---'}}</h2>
                    </div>
                    <div class="col-6 text-left align-middle py-2">
                        <h2 class="h5 text-white">{{ticket.created_at|stringifyTime}}</h2>
                    </div>
                </div>
                <div class="col-12 pt-2 px-4 mb-4">
                    <span class="text-right" v-html="ticket.text">{{}}</span>
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
    import moment from 'moment';
    import Notifications from "../../elements/Notifications";
    import Editor from '@tinymce/tinymce-vue';
    import Errors from "../../../includes/Errors";

    export default {
        name: "EditTicket",
        components : {
            'editor': Editor,
            'Notifications': Notifications,
        },
        data() {
            return {
                'fields': {
                    'title': '',
                    'receiver_id': '',
                    'receiver_type': 'User',
                    'type': 'support',
                    'status': 'new',
                    'text':'',
                    'ticket_items':[],
                    'updated_at':''
                },
                'defaultFieldsValue': {
                    'title': '',
                    'receiver_id': '',
                    'receiver_type': 'User',
                    'type': 'support',
                    'status': 'new',
                    'text':'',
                    'ticket_items':[],
                    'updated_at':''
                },
                'file': null,
                'in_progress': false,
                'upload_progress': false,
                'progress_bar_value': 0,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api',
            };
        },
        computed: {
            stringTime: function() {
                // return this.fields.updated_at | fromNow;
                return this.$options.filters.fromNow(this.fields.updated_at)
            }
        },
        methods : {
            getTicket() {
                window.axios.post(this.base_url + '/get-ticket', {
                    'ticket_id': this.$route.params.id
                })
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.fields = response.data.data.record;
                            this.fields.text = ''
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
            save() {
                this.errors.reset();
                this.message = '';
                let _this = this;
                let data = {};
                window.axios.post(this.base_url + '/reply-ticket', {...this.fields,'ticket_id':this.$route.params.id})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
                            this.getTicket();
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
            this.getTicket();
        },
        filters: {
            fromNow: function (date) {
                    moment.locale('fa');
                    return moment(date).fromNow(); ;
                },
            stringifyTime: function(date) {
                    moment.locale('fa');
                    return moment(date).fromNow(); ;
                },
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
