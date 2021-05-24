<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">شارژ کیف پول</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('user_id')}">
                    <label for="user" class="font-15 bold-600 mb-2">انتخاب کاربر</label>
                    <select id="user" name="user" v-model="fields.user_id" class="form-control">
                        <option v-for="user in users" :key="user.id+user.type" :value="user.id" :selected="user.id ==fields.user_id">{{user.name?user.name:user.mobile}}</option>
                    </select>
                    <span class="error-text" v-if="errors.has('user_id')">{{ errors.get('user_id')}}</span>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('type')}">
                    <label class="font-15 bold-600 mb-2" for="type">وضعیت</label>
                    <select v-model="fields.type" id="type" name="type" class="form-control">
                        <option value="charge">شارژ کیف پول کاربر</option>
                        <option value="penalty">کسر از حساب کاربر</option>
                    </select>
                    <span class="error-text" v-if="errors.has('type')">{{ errors.get('type')}}</span>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('amount')}" >
                    <label class="font-15 bold-600 mb-2" for="amount">مبلغ</label>
                    <input type="number" v-model="fields.amount" id="amount" name="amount" class="form-control" placeholder="مثلا 10000">
                    <span class="error-text" v-if="errors.has('amount')">{{ errors.get('amount')}}</span>
                    <small class="form-text text-muted">مبلغ را به صورت عددی و بدون واحد پول درج کنید.</small>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('description')}">
                    <label class="font-15 bold-600 mb-2" for="description">توضیحات</label>
                    <input type="text" v-model="fields.description" id="description" name="description" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('description')">{{ errors.get('description')}}</span>
                    <small class="form-text text-muted">توضیحات برای کاربر نمایش داده خواهد شد</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary rounded py-2 px-5 font-14" @click="save()">ثبت تراکنش</button>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import Notifications from "../../elements/Notifications";
    import Errors from "../../../includes/Errors";

    export default {
        name: "WalletCharge",
        components : {
            'Notifications': Notifications,
        },
        data() {
            return {
                'fields': {
                    'user_id': null,
                    'type': 'charge',
                    'amount':null,
                    'description':null,
                },
                'defaultFieldsValue': {
                    'user_id': null,
                    'type': 'charge',
                    'amount':null,
                    'description':null,
                },
                'users':[],
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/',
                'mainCategories': [],
            };
        },
        computed: {
        },
        methods : {
            getUsers() {
                window.axios.post(this.base_url + 'product/get-users', {})
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
            save() {
                this.errors.reset();
                this.message = '';
                let _this = this;

                window.axios.post(this.base_url + 'transactions/create', this.fields)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            console.log('aaa')
                            this.message = response.data.message;
                            this.fields = this.defaultFieldsValue;
                        }
                        else
                        {
                            try {
                                if (Object.keys(response.data.data.errors).length)
                                {
                                    this.errors.record(response.data.data.errors);
                                }
                                else
                                {
                                    this.errors.record({'general': response.data.message});
                                }
                            } catch (e) {
                                this.errors.record({'general': [response.data.message]});
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
            // this.getInfo();
            this.getUsers();
        },
        filters: {
            fromNow: function (date) {
                    moment.locale('fa');
                    return moment(date).fromNow(); ;
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
