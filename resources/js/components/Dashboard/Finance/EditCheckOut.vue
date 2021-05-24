<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">رسیدگی به درخواست تسویه حساب</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">

            <div class="col-md-8">
                <div class="form-group mb-4" >
                    <label class="font-15 bold-600 mb-2" for="text">توضیحات</label>
                    <input type="text" v-model="fields.text" id="text" name="text" class="form-control" placeholder="">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('status')}">
                    <label class="font-15 bold-600 mb-2" for="status">وضعیت</label>
                    <select v-model="fields.status" id="status" name="status" class="form-control">
                        <option value="new">در انتظار</option>
                        <option value="success">پرداخت شده</option>
                        <option value="canceled">لغو شده</option>
                    </select>
                    <span class="error-text" v-if="errors.has('status')">{{ errors.get('status')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-4" >
                    <label class="font-15 bold-600 mb-2" for="username">نام کاربر</label>
                    <input v-if="fields.user" type="text" v-model="fields.user.name" id="username" name="username" class="form-control" placeholder="" disabled>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2" for="amount">مبلغ</label>
                    <input type="number" v-model="fields.amount" id="amount" name="amount" class="form-control" placeholder="" disabled>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2" for="created_at">زمان</label>
                    <input type="text" v-model="stringTime" id="created_at" name="created_at" class="form-control" placeholder="" disabled>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-4" >
                    <label class="font-15 bold-600 mb-2" for="bank">نام بانک</label>
                    <input type="text" v-model="fields.bank" id="bank" name="bank" class="form-control" placeholder="" disabled>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2" for="card">شماره کارت</label>
                    <input type="text" v-model="fields.card" id="card" name="card" class="form-control" placeholder="" disabled>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4">
                    <label class="font-15 bold-600 mb-2" for="sheba">شماره شبا</label>
                    <input type="text" v-model="fields.sheba" id="sheba" name="sheba" class="form-control" placeholder="" disabled>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="save()">ذخیره</button>
                <router-link :to="{name: 'finance.checkout'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
                    <span>بازگشت</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import Notifications from "../../elements/Notifications";
    import Errors from "../../../includes/Errors";

    export default {
        name: "EditCheckOut",
        components : {
            'Notifications': Notifications,
        },
        data() {
            return {
                'fields': {
                    'text': '',
                    'status': '',
                    'user': {
                        'id': '',
                        'name': ''
                    },
                    'amount': 0,
                    'created_at': '',
                    'bank': '',
                    'card': '',
                    'sheba': '',
                    'updated_at':''
                },
                'defaultFieldsValue': {
                    'text': '',
                    'status': '',
                    'user': {
                        'id': '',
                        'name': ''
                    },
                    'amount': 0,
                    'created_at': '',
                    'bank': '',
                    'card': '',
                    'sheba': '',
                    'updated_at':''
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/checkout',
                'mainCategories': [],
            };
        },
        computed: {
            stringTime: function() {
                moment.locale('fa');
                return moment(this.fields.updated_at).fromNow();
            }
        },
        methods : {
            getInfo() {
                window.axios.post(this.base_url + '/show/' + this.$route.params.id, {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.fields = response.data.data.record;
                            if(this.fields.transaction) {
                                this.fields.text = this.fields.transaction.text
                            }
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
                this.errors.reset();
                this.message = '';
                let _this = this;
                let data = {};
                data = {...this.fields}
                window.axios.post(this.base_url + '/save/' + this.$route.params.id, data)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
                            // this.fields = JSON.parse(JSON.stringify(this.defaultFieldsValue));
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
        filters: {
            fromNow: function (date) {
                    moment.locale('fa');
                    return moment(date).fromNow();
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
