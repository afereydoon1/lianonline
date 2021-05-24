<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">افزودن دسته بندی جدید</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-5">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('title')}">
                    <label class="font-15 bold-600 mb-2" for="title">عنوان دسته بندی</label>
                    <input type="text" v-model="fields.title" id="title" name="title" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('title')">{{ errors.get('title')}}</span>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('parent_id')}">
                    <label class="font-15 bold-600 mb-2" for="parent_id">والد</label>
                    <treeselect dir="rtl"
                                :multiple="false"
                                :options="mainAssortments"
                                :flat="true"
                                placeholder="دسته بندی نوشته"
                                v-model="fields.parent_id"
                                name="parent_id"
                                id="parent_id"
                                class="form-control"
                        />
                    <!--
                    <select v-model="fields.parent_id" id="parent_id" name="parent_id" class="form-control">
                        <option value="0">دسته بندی اصلی</option>
                        <option disabled>--------------</option>
                        <option v-for="(key,value) in mainAssortments" :value="key">{{value}}</option>
                    </select>-->
                    <span class="error-text" v-if="errors.has('parent_id')">{{ errors.get('parent_id')}}</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('order')}">
                    <label class="font-15 bold-600 mb-2" for="order">اولویت</label>
                    <input type="number" v-model="fields.order" id="order" name="order" class="form-control ltr" placeholder="" min="1">
                    <span class="error-text" v-if="errors.has('order')">{{ errors.get('order')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('briefly')}">
                    <label class="font-15 bold-600 mb-2" for="briefly">خلاصه توضیحات</label>
                    <textarea v-model="fields.briefly" id="briefly" name="briefly" class="form-control" placeholder=""></textarea>
                    <span class="error-text" v-if="errors.has('briefly')">{{ errors.get('briefly')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="saveAssortment()">ذخیره</button>
                <router-link :to="{name: 'assortment.list'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
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
    export default {
        name: "CreateAssortment",
        components : {
            'editor': Editor,
            'Notifications': Notifications,
            Treeselect
        },
        data() {
            return {
                'fields': {
                    'title': '',
                    'briefly': '',
                    'icon': '',
                    'image': '',
                    'parent_id': 0,
                    'order': 1,
                    'status' : 'active'
                },
                'defaultFieldsValue': {
                    'title': '',
                    'briefly': '',
                    'icon': '',
                    'image': '',
                    'parent_id': 0,
                    'order': 1,
                    'status' : 'active'
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/assortment',
                'mainAssortments': []
            };
        },
        methods : {
            getMainAssortments() {
                window.axios.post(this.base_url + '/main-assortments', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.mainAssortments = [];
                            this.mainAssortments = [
                                {
                                    'id': '0',
                                    'label': 'دسته بندی اصلی'
                                },
                                ...response.data.data.records
                            ];
                        }
                        else
                        {
                            this.mainAssortments = [];
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
            saveAssortment() {
                this.errors.clear('general');
                this.message = '';
                let _this = this;
                let data = {};
                Object.keys(this.fields).forEach(function(key) {
                    if (_this.fields[key] !== '' && _this.fields[key] !== 0) {
                        data[key] = _this.fields[key];
                    }
                });

                window.axios.post(this.base_url + '/create', data)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
                            this.fields = JSON.parse(JSON.stringify(this.defaultFieldsValue));
                            this.getMainAssortments();
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
            }
        },
        mounted() {
            this.getMainAssortments();
        }
    }
</script>

<style scoped>

</style>
