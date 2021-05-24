<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">مشاهده ی سفارش</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('customer_name')}">
                    <label class="font-15 bold-600 mb-2" for="customer_name">نام مشتری</label>
                    <input type="text" v-model="fields.customer_name" id="customer_name" name="customer_name"
                           class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('customer_name')">{{ errors.get('customer_name')}}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('customer_email')}">
                    <label class="font-15 bold-600 mb-2" for="customer_email">ایمیل مشتری</label>
                    <input type="text" v-model="fields.customer_email" id="customer_email" name="customer_email"
                           class="form-control" placeholder="">
                    <span class="error-text"
                          v-if="errors.has('customer_email')">{{ errors.get('customer_email')}}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('customer_mobile')}">
                    <label class="font-15 bold-600 mb-2" for="customer_mobile">موبایل مشتری</label>
                    <input type="text" v-model="fields.customer_mobile" id="customer_mobile" name="customer_mobile"
                           class="form-control" placeholder="">
                    <span class="error-text"
                          v-if="errors.has('customer_mobile')">{{ errors.get('customer_mobile')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('customer_state')}">
                    <label class="font-15 bold-600 mb-2" for="customer_state">استان</label>
                    <input type="text" v-model="fields.customer_state" id="customer_state" name="customer_state"
                           class="form-control" placeholder="">
                    <span class="error-text"
                          v-if="errors.has('customer_state')">{{ errors.get('customer_state')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('customer_city')}">
                    <label class="font-15 bold-600 mb-2" for="customer_city">شهرستان</label>
                    <input type="text" v-model="fields.customer_city" id="customer_city" name="customer_city"
                           class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('customer_city')">{{ errors.get('customer_city')}}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('customer_address')}">
                    <label class="font-15 bold-600 mb-2" for="customer_address">آدرس</label>
                    <input type="text" v-model="fields.customer_address" id="customer_address" name="customer_address"
                           class="form-control" placeholder="">
                    <span class="error-text"
                          v-if="errors.has('customer_address')">{{ errors.get('customer_address')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('customer_postal_code')}">
                    <label class="font-15 bold-600 mb-2" for="customer_postal_code">کد پستی</label>
                    <input type="number" v-model="fields.customer_postal_code" id="customer_postal_code" name="customer_postal_code"
                           class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('customer_postal_code')">{{ errors.get('customer_postal_code')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('total_price')}">
                    <label class="font-15 bold-600 mb-2" for="total_price">مبلغ</label>
                    <input type="text" v-model="fields.total_price" id="total_price" name="total_price"
                           class="form-control" placeholder="" disabled>
                    <span class="error-text" v-if="errors.has('total_price')">{{ errors.get('total_price')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('status')}">
                    <label class="font-15 bold-600 mb-2" for="status">وضعیت</label>
                    <select v-model="fields.status" id="status" name="status" class="form-control">
                        <option value="new">جدید</option>
                        <option value="ok">موفق</option>
                        <option value="canceled">لغو شده</option>
                        <option value="failed">خطا در تکمیل سفارش</option>
                    </select>
                    <span class="error-text" v-if="errors.has('status')">{{ errors.get('status')}}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('order_date')}">
                    <label class="font-15 bold-600 mb-2" for="order_date">تاریخ</label>
                    <input type="text" v-model="fields.order_date" id="order_date" name="order_date" class="form-control"
                           placeholder="" disabled>
                    <span class="error-text" v-if="errors.has('order_date')">{{ errors.get('order_date')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div v-if="fields.order_items.length === 0">
                    <p class="alert alert-warning">داده ای جهت نمایش وجود ندارد</p>
                </div>
                <div v-if="fields.order_items.length > 0">
                    <table class="lian-table table table-hover table-borderless border-0">
                        <thead>
                        <tr>
                            <th class="sm" scope="col">#</th>
                            <th class="md" scope="col">عنوان محصول</th>
                            <th class="md" scope="col">نوع محصول</th>
                            <th class="md" scope="col">قیمت پایه</th>
                            <th class="md" scope="col">تعداد</th>
                            <th class="md" scope="col">قیمت نهایی</th>
                            <th class="md" ></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(record, index) in fields.order_items" :key="record.product_title+record.id">
                            <th scope="row">{{index+1}}</th>
                            <td>{{record.product_title}}</td>
                            <td>{{record.type}}</td>
                            <td>{{record.product_price}}</td>
                            <td>{{record.product_total}}</td>
                            <td>{{record.product_total * record.product_price}}</td>
                            <td><span class="btn btn-danger border" @click="delFromItems(record)">حذف</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <h2 class="h5 mx-4 text-muted">اضافه کردن محصول به لیست فوق</h2>
        </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group mb-4">
                    <input type="number" v-model="search.id" id="name" name="name" class="form-control" placeholder="آیدی" >
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4">
                    <input type="text" v-model="search.title" id="title" name="title" class="form-control" placeholder="عنوان" >
                </div>
            </div>
            <div class="col-md-1">
                 <a class="btn btn-sm btn-success text-white d-block" @click="searchProducts()">جستجو</a>
            </div>
        </div>
        <div class="row mx-3" v-if="this.search_records.length > 0">
                <table class="lian-table table table-hover table-borderless border-0">
                    <thead>
                    <tr>
                        <th class="sm" scope="col">#</th>
                        <th class="md" scope="col">عنوان محصول</th>
                        <th class="md" scope="col">شناسه محصول</th>
                        <th class="md"> </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="record in search_records" :key="record.title">
                        <th scope="row">{{record.id}}</th>
                        <td>
                            <a :href="'https://www.lianonline.ir/market/product/'+record.id" target="_blank"><span>{{record.title}}</span></a>
                        </td>
                        <td>{{record.key}}</td>
                        <td>
                            <span class="btn btn-info border" @click="addToProducts(record)">اضافه شود</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
               <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="saveOrder()">ذخیره</button>
                <router-link :to="{name: 'orders.list'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
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
        name: "ShowOrder",
        components: {
            'editor': Editor,
            'Notifications': Notifications
        },
        data() {
            return {
                'fields': {
                    'id': '',
                    'callback_url': '',
                    'created_at': '',
                    'customer_address': '',
                    'customer_city': '',
                    'customer_email': '',
                    'customer_mobile': '',
                    'customer_name': '',
                    'customer_state': '',
                    'customer_postal_code': '',
                    'order_items': [],
                    'payments': '',
                    'referrer_id': '',
                    'status': '',
                    'total_price': '',
                    'updated_at': '',
                    'user': '',
                    'user_id': '',
                },
                'defaultFieldsValue': {
                    'id': '',
                    'callback_url': '',
                    'created_at': '',
                    'customer_address': '',
                    'customer_city': '',
                    'customer_email': '',
                    'customer_mobile': '',
                    'customer_name': '',
                    'customer_state': '',
                    'customer_postal_code': '',
                    'order_items': [],
                    'payments': '',
                    'referrer_id': '',
                    'status': '',
                    'total_price': '',
                    'updated_at': '',
                    'user': '',
                    'user_id': '',
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/orders',
                'search': {
                    'id':'',
                    'title':'',
                },
                'search_records':[],
            };
        },
        methods: {
            getOrderInfo() {
                window.axios.post(this.base_url + '/show/' + this.$route.params.id, {})
                    .then(response => {
                        if (response.data.returnCode === 0) {
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
            saveOrder() {
                window.axios.post(this.base_url+'/save',this.fields)
                .then(response => {
                        if (response.data.returnCode === 0) {
                            this.message = response.data.message;
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
            searchProducts() {
                this.search_records = [];
                 window.axios.post(this.base_url+'/search-products', this.search)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.search_records = response.data.data.records;
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
            addToProducts(Product) {
                var location = this.fields.order_items.findIndex((order_item)=>order_item.product_id == Product.id);

                if(location != -1) {
                    var product_total = this.fields.order_items[location]['product_total'];
                    this.fields.order_items[location]['product_total'] = product_total +1;
                }else{
                    var new_order_item = {
                        'id': null,
                        'marketer_percent': Product.marketer_percent,
                        'order_id': this.$route.params.id,
                        'product_id': Product.id,
                        'product_price': Product.price,
                        'product_title': Product.title,
                        'product_total': 1,
                        'product_weight': Product.weight,
                        'type': Product.type,
                    }
                    this.fields.order_items = [...this.fields.order_items,new_order_item]
                }

            },
            delFromItems(item) {
                const index = this.fields.order_items.findIndex((items) => items.id == item.id);
                this.fields.order_items.splice(index,1);
            }
        },
        mounted() {
            this.getOrderInfo();
        },
    }
</script>

<style lang="sass">

</style>
