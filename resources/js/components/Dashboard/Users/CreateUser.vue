<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">افزودن کاربر جدید</h2>
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
                <div class="form-group mb-4" :class="{'has-error' : errors.has('avatar')}">
                    <label class="font-15 bold-600 mb-2" for="avatar">آواتار</label>
                    <div style="min-height:100px;cursor: pointer;" class="form-control" @click="openUpload('avatar')">
                        <img v-if="fields.avatar" :src="fields.avatar">
                    </div>
                    <span v-if="fields.avatar" class="btn btn-sm btn-outline-danger rounded mt-1 d-block" @click="delPhoto('avatar')">حذف عکس</span>
                    <input type="file" id="avatar" name="avatar" @change="uplaodFile($event,'avatar')" style="display: none" ref="avatar">
                    <span class="error-text" v-if="errors.has('avatar')">{{ errors.get('avatar')}}</span>
                </div>
            </div>
            <div class="col-md-9">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('bio')}">
                    <label class="font-15 bold-600 mb-2" for="bio">بیوگرافی</label>
                    <textarea v-model="fields.bio" id="bio" name="bio" class="form-control" placeholder=""></textarea>
                    <span class="error-text" v-if="errors.has('bio')">{{ errors.get('bio')}}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('bank')}">
                    <label class="font-15 bold-600 mb-2" for="bank">نام بانک</label>
                    <input type="text" v-model="fields.bank" id="bank" name="bank" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('bank')">{{ errors.get('bank')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('bank_account_number')}">
                    <label class="font-15 bold-600 mb-2" for="bank_account_number">شماره حساب</label>
                    <input type="text" v-model="fields.bank_account_number" id="bank_account_number" name="bank_account_number" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('bank_account_number')">{{ errors.get('bank_account_number')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('card_number')}">
                    <label class="font-15 bold-600 mb-2" for="card_number">شماره کارت</label>
                    <input type="text" v-model="fields.card_number" id="card_number" name="card_number" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('card_number')">{{ errors.get('card_number')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('sheba')}">
                    <label class="font-15 bold-600 mb-2" for="sheba">شماره شبا</label>
                    <input type="text" v-model="fields.sheba" id="sheba" name="sheba" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('sheba')">{{ errors.get('sheba')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('state')}">
                    <label class="font-15 bold-600 mb-2" for="state">استان</label>
                    <input type="text" v-model="fields.state" id="state" name="state" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('state')">{{ errors.get('state')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('city')}">
                    <label class="font-15 bold-600 mb-2" for="city">شهر</label>
                    <input type="text" v-model="fields.city" id="city" name="city" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('city')">{{ errors.get('city')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('address')}">
                    <label class="font-15 bold-600 mb-2" for="address">آدرس</label>
                    <input type="text" v-model="fields.address" id="address" name="address" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('address')">{{ errors.get('address')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('postal_code')}">
                    <label class="font-15 bold-600 mb-2" for="postal_code">کد پستی</label>
                    <input type="text" v-model="fields.postal_code" id="postal_code" name="postal_code" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('postal_code')">{{ errors.get('postal_code')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
           <div class="col-md-6">
               <div class="form-group mb-4" :class="{'has-error' : errors.has('national_card_image')}">
                    <label class="font-15 bold-600 mb-2" for="national_card_image">تصویر کارت ملی</label>
                    <div style="min-height:100px;cursor: pointer;" class="form-control" @click="openUpload('national_card')">
                        <img v-if="fields.national_card_image" :src="fields.national_card_image">
                    </div>
                    <span v-if="fields.national_card_image" class="btn btn-sm btn-outline-danger rounded mt-1 d-block" @click="delPhoto('national_card_image')">حذف عکس</span>
                    <input type="file" id="national_card_image" name="national_card_image" @change="uplaodFile($event,'national_card')" style="display: none" ref="national_card">
                    <span class="error-text" v-if="errors.has('national_card_image')">{{ errors.get('national_card_image')}}</span>
                </div>
           </div>
           <div class="col-md-6">
               <div class="form-group mb-4" :class="{'has-error' : errors.has('credit_card_image')}">
                    <label class="font-15 bold-600 mb-2" for="credit_card_image">تصویر کارت بانکی</label>
                    <div style="min-height:100px;cursor: pointer;" class="form-control" @click="openUpload('credit_card')">
                        <img v-if="fields.credit_card_image" :src="fields.credit_card_image">
                    </div>
                    <span v-if="fields.credit_card_image" class="btn btn-sm btn-outline-danger rounded mt-1 d-block" @click="delPhoto('credit_card_image')">حذف عکس</span>
                    <input type="file" id="credit_card_image" name="credit_card_image" @change="uplaodFile($event,'credit_card')" style="display: none" ref="credit_card">
                    <span class="error-text" v-if="errors.has('credit_card_image')">{{ errors.get('credit_card_image')}}</span>
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
    import Treeselect from '@riophae/vue-treeselect';
    import MshSelect from "../../elements/mshSelect";


    export default {
        name: "CreateUser",
        components : {
            'editor': Editor,
            'Notifications': Notifications,
            'treeselect' : Treeselect,
            'MshSelect' : MshSelect
        },
        data() {
            return {
                'fields': {
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
                'mainAssortments': [],
                'assortments': [],
                'tags': [],

            };
        },
        methods : {
            saveUser() {
                this.errors.clear('general');
                this.message = '';
                window.axios.post('/management/api/user/create', this.fields)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
                            this.fields = JSON.parse(JSON.stringify(this.defaultFieldsValue));
                        }
                        else
                        {
                            if(response.data.data != undefined) {
                                if (Object.keys(response.data.data.errors).length)
                                {
                                    this.errors.record(response.data.data.errors);
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

        },
    }
</script>

<style lang="sass">

</style>
