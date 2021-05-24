<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">تنظیمات عمومی</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('site_name')}">
                    <label class="font-15 bold-600 mb-2" for="site_name">عنوان سایت</label>
                    <input type="text" v-model="fields.site_name" id="site_name" name="site_name" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('site_name')">{{ errors.get('site_name')}}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('favicon_url')}">
                    <label class="font-15 bold-600 mb-2" for="favicon_url">آدرس آیکن</label>
                    <input type="text" v-model="fields.favicon_url" id="favicon_url" name="favicon_url" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('favicon_url')">{{ errors.get('favicon_url')}}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('logo_url')}">
                    <label class="font-15 bold-600 mb-2" for="logo_url">آدرس لوگو</label>
                    <input type="text" v-model="fields.logo_url" id="logo_url" name="logo_url" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('logo_url')">{{ errors.get('logo_url')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('site_description')}">
                    <label class="font-15 bold-600 mb-2" for="site_description">توضیحات</label>
                    <textarea v-model="fields.site_description" id="site_description" name="site_description" class="form-control" placeholder=""></textarea>
                    <span class="error-text" v-if="errors.has('site_description')">{{ errors.get('site_description')}}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('gateway')}">
                    <label class="font-15 bold-600 mb-2" for="gateway">GateWay</label>
                    <textarea v-model="fields.gateway" id="gateway" name="gateway" class="form-control" placeholder=""></textarea>
                    <span class="error-text" v-if="errors.has('gateway')">{{ errors.get('gateway')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h4 class="h5">Licenses :</h4>
                <button class="btn btn-secondary rounded-pill py-2 px-5 font-14" @click="addLicense()">افزودن</button>
            </div>
        </div>
        <div class="row" v-for="(item, index) in fields.licenses">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('licenses')}">
                    <label class="font-15 bold-600 mb-2" for="licenses">عنوان :</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <input type="text" v-model="fields.licenses[index].key">
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" @click="removeLicense(index)" type="button">حذف</button>
                        </div>
                    </div>
                    <textarea v-model="fields.licenses[index].value" id="licenses" name="licenses" class="form-control" placeholder=""></textarea>
                    <span class="error-text" v-if="errors.has('licenses')">{{ errors.get('licenses')}}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('about_us')}">
                    <label class="font-15 bold-600 mb-2" for="about_us">درباره ی ما</label>
                    <editor v-model="fields.about_us" tinymce-script-src="/libraries/tinymce/tinymce.min.js" id="about_us"
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
                    <span class="error-text" v-if="errors.has('about_us')">{{ errors.get('about_us')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('contact_us')}">
                    <label class="font-15 bold-600 mb-2" for="contact_us">ارتباط با ما</label>
                    <editor v-model="fields.contact_us" tinymce-script-src="/libraries/tinymce/tinymce.min.js" id="contact_us"
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
                    <span class="error-text" v-if="errors.has('contact_us')">{{ errors.get('contact_us')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('terms_and_conditions')}">
                    <label class="font-15 bold-600 mb-2" for="terms_and_conditions">قوانین و مقررات</label>
                    <editor v-model="fields.terms_and_conditions" tinymce-script-src="/libraries/tinymce/tinymce.min.js" id="terms_and_conditions"
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
                    <span class="error-text" v-if="errors.has('terms_and_conditions')">{{ errors.get('terms_and_conditions')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('site_percent')}">
                    <label class="font-15 bold-600 mb-2" for="site_percent">درصد پورسانت سایت</label>
                    <input type="number" v-model="fields.site_percent" id="site_percent" name="site_percent" class="form-control ltr" placeholder="" min="0" max="40">
                    <span class="error-text" v-if="errors.has('site_percent')">{{ errors.get('site_percent')}}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="saveSetting()">ذخیره</button>
                <router-link :to="{name: 'dashboard'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
                    <span>انصراف</span>
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
        name: "General",
        components : {
            'editor': Editor,
            'Notifications': Notifications
        },
        data() {
            return {
                'fields': {
                    'favicon_url': "",
                    'gateway': "",
                    'licenses': {},
                    'logo_url': "",
                    'site_description': "",
                    'site_name': "",
                    'about_us': "",
                    'contact_us': "",
                    'terms_and_conditions': "",
                    'site_percent': 0
                },
                'defaultFieldsValue': {
                    'favicon_url': "",
                    'gateway': "",
                    'licenses': {},
                    'logo_url': "",
                    'site_description': "",
                    'site_name': "",
                    'about_us': "",
                    'contact_us': "",
                    'terms_and_conditions': "",
                    'site_percent': 0
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/settings',

            };
        },
        methods : {
            addLicense() {
                let name = 'license_' + this.fields.licenses.length;
                this.fields.licenses.push({"key": name, "value": ''});
            },
            removeLicense(index) {
                let a = confirm('از حذف این گزینه اطمینان دارید؟');

                if (a) {
                    this.fields.licenses.splice(index,1);
                }
            },
            getSetting() {
                window.axios.post(this.base_url + '/general', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.fields = response.data.data.record;

                            let licenses = JSON.parse(JSON.stringify(this.fields.licenses));

                            this.fields.licenses = [];
                            var _this = this;

                            Object.keys(licenses).forEach(function(key) {
                                _this.fields.licenses.push({"key": key,"value": licenses[key]});
                            });
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
            saveSetting() {
                this.errors.clear('general');
                this.message = '';
                window.axios.post(this.base_url + '/general/save', this.fields)
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
            this.getSetting();
        },
    }
</script>

<style lang="sass">

</style>
