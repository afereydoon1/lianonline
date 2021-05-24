<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">ویرایش ادمین</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('name')}">
                    <label class="font-15 bold-600 mb-2" for="name">نام و نام خانوادگی</label>
                    <input type="text" v-model="fields.name" id="name" name="name" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('name')">{{ errors.get('name')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('username')}">
                    <label class="font-15 bold-600 mb-2" for="username">شناسه کاربری</label>
                    <input type="text" v-model="fields.username" id="username" name="username" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('username')">{{ errors.get('username')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('mobile')}">
                    <label class="font-15 bold-600 mb-2" for="mobile">موبایل</label>
                    <input type="text" v-model="fields.mobile" id="mobile" name="mobile" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('mobile')">{{ errors.get('mobile')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('email')}">
                    <label class="font-15 bold-600 mb-2" for="email">ایمیل</label>
                    <input type="text" v-model="fields.email" id="email" name="email" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('email')">{{ errors.get('email')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('password')}">
                    <label class="font-15 bold-600 mb-2" for="password">رمزعبور</label>
                    <input type="text" v-model="fields.password" id="password" name="password" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('password')">{{ errors.get('password')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('status')}">
                    <label class="font-15 bold-600 mb-2" for="status">وضعیت</label>
                    <select v-model="fields.status" id="status" name="status" class="form-control">
                        <option value="active">فعال</option>
                        <option value="deActive">غیرفعال</option>
                    </select>
                    <span class="error-text" v-if="errors.has('status')">{{ errors.get('status')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="saveUser()">ذخیره</button>
                <router-link :to="{name: 'users.list'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
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
        name: "EditAdmin",
        components : {
            'editor': Editor,
            'Notifications': Notifications
        },
        data() {
            return {
                'fields': {
                    'id': '',
                    'name': '',
                    'username': '',
                    'email': '',
                    'mobile': '',
                    'avatar': '',
                    'state': '',
                    'city': '',
                    'postal_code': '',
                    'card_number': '',
                    'sheba': '',
                    'bank_account_number':'',
                    'national_card_image': '',
                    'credit_card_image': '',
                    'bank': '',
                    'address': '',
                    'bio': '',
                    'role_id': '',
                    'email_verified_at': '',
                    'mobile_verified_at': '',
                    'password': '',
                    'status': 'active'
                },
                'defaultFieldsValue': {
                    'id': '',
                    'name': '',
                    'username': '',
                    'email': '',
                    'mobile': '',
                    'avatar': '',
                    'state': '',
                    'city': '',
                    'postal_code': '',
                    'card_number': '',
                    'sheba': '',
                    'bank_account_number':'',
                    'national_card_image': '',
                    'credit_card_image': '',
                    'bank': '',
                    'address': '',
                    'bio': '',
                    'role_id': '',
                    'email_verified_at': '',
                    'mobile_verified_at': '',
                    'password': '',
                    'status': 'active'
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/admin',

            };
        },
        methods : {
            getUserInfo() {
                window.axios.post(this.base_url + '/show/' + this.$route.params.id, {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.fields = response.data.data.record;
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
            saveUser() {
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
            openUpload(section) {
                switch(section) {
                    case 'avatar':
                        this.$refs.avatar.click()
                        break;
                    case 'national_card':
                        this.$refs.national_card.click()
                        break;
                    case 'credit_card':
                        this.$refs.credit_card.click()
                        break;
                }
            },
            delPhoto(whichSection) {
                switch (whichSection) {
                    case 'avatar':
                        this.fields.avatar = "";
                        break;
                    case 'national_card_image':
                        this.fields.national_card_image = "";
                        break;
                    case 'credit_card_image':
                        this.fields.credit_card_image = "";
                        break;
                    default:
                        break;
                }
            },
            uplaodFile(event,section) {
                var files;
                switch(section) {
                    case 'avatar':
                        this.fields.avatar = '';
                        files = this.$refs.avatar.files
                        break;
                    case 'national_card':
                        this.fields.national_card_image = '';
                        files = this.$refs.national_card.files
                        break;
                    case 'credit_card':
                        this.fields.credit_card_image = '';
                        files = this.$refs.credit_card.files
                        break;
                }
                if(files.length > 0) {
                        const file = files[0];
                        var formData = new FormData();
                        formData.append('type',`user-file-${section}`)
                        formData.append('file',file)
                        window.axios.post('/management/api/user/upload-image', formData )
                        .then(response => {
                            if (response.data.returnCode === 0)
                            {
                                switch(section) {
                                    case 'avatar':
                                        this.fields.avatar = response.data.data.avatar;
                                        break;
                                    case 'national_card':
                                        this.fields.national_card_image = response.data.data.national_card;
                                        break;
                                    case 'credit_card':
                                        this.fields.credit_card_image = response.data.data.credit_card;
                                        break;
                                }
                            }
                            else
                            {
                                if(response.data.data != undefined) {
                                    if (Object.keys(response.data.data.errors).length)
                                    {
                                        switch(section) {
                                            case 'avatar':
                                                this.errors.record({'avatar': response.data.data.errors.file});
                                                break;
                                            case 'national_card':
                                                this.errors.record({'national_card_image': response.data.data.errors.file});
                                                break;
                                            case 'credit_card':
                                                this.errors.record({'credit_card_image': response.data.data.errors.file});
                                                break;
                                        }
                                    }else
                                    {
                                        this.errors.record({'general': response.data.message});
                                    }
                                }
                                else
                                {
                                    this.errors.record({'general': response.data.message});
                                }
                            }
                        })
                        .catch(error => {
                        console.log(error);
                        this.errors.handle(this, error);
                        });

                }
            }
        },
        mounted() {
            this.getUserInfo();
        },
    }
</script>

<style lang="sass">

</style>
