<template>
    <div>
        <div class="row">
            <div class="col-sm-7 text-right pt-2">
                <h2 class="h4 mb-4 text-info">لیست نوشته ها</h2>
            </div>
            <div class="col-sm-5 text-left">
                <router-link :to="{name: 'posts.create'}" class="btn btn-info m-1 item ">
                <i class="fas fa-plus"></i> افزودن نوشته ی جدید
                </router-link>
            </div>
        </div>

        <notifications :error="errors.get('general')" :message="message"></notifications>
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
            <div class="col-md-3">
                <div class="form-group mb-4">
                    <input type="text" v-model="search.key" id="key" name="key" class="form-control" placeholder="شناسه" >
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-4">
                    <select v-model="search.show_status" id="show_status" name="show_status" class="form-control">
                        <option value="2">همه</option>
                        <option value="1">نمایش</option>
                        <option value="0">عدم نمایش</option>
                    </select>
                </div>
            </div>
            <div class="col-md-1">
                 <a class="btn btn-sm btn-success text-white d-block" @click="getList(base_url)">جستجو</a>
            </div>
        </div>
        <div v-if="this.records.total === 0">
            <p class="alert alert-warning">داده ای جهت نمایش وجود ندارد</p>
        </div>
        <div v-if="this.records.total > 0">
            <table class="lian-table table table-hover table-borderless border-0">
                <thead>
                <tr>
                    <th class="sm" scope="col">#</th>
                    <th class="md" scope="col">عنوان نوشته</th>
                    <th class="md" scope="col">کاربر</th>
                    <th class="md" scope="col">زمان</th>
                    <th class="md" scope="col">دسته</th>
                    <th class="md" scope="col">برچسب</th>
                    <th class="md text-center" scope="col">عملیات</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(record, index) in records.data" :key="record.id">
                    <th scope="row">{{record.id}}</th>
                    <td>
                        <!-- <router-link :to="{name: 'posts.edit', params: {'id': record.id}}">
                            <span>{{record.title}}</span>
                        </router-link> -->
                        <a :href="'https://www.lianonline.ir/blog/post/'+record.id" target="_blank"><span>{{record.title}}</span></a>
                    </td>
                    <td>
                        <router-link  v-if="record.creator" :to="{name: 'users.edit', params: {'id': record.creator.id}}">
                            <span>{{record.creator.name ? record.creator.name : record.creator.mobile}}</span>
                        </router-link>
                    </td>
                    <td>{{record.created_at | fromNow}}</td>
                    <td>
                        <router-link v-for="(item,i) in record.assortments" :key="item.title" :to="{name: 'assortment.edit', params: {'id': item.id}}">
                            <span >{{item.title}}{{i == record.assortments.length -1 ?  '': ' - '}}</span>
                        </router-link>
                    </td>
                    <td>
                        <!-- <router-link v-for="item in record.tags" :key="item.name" :to="{name: 'tags.edit', params: {'id': item.id}}">
                            <span >{{item.name}} </span>
                        </router-link> -->
                        <span >{{record.tags.length > 0?  'دارد': 'ندارد'}}</span>
                    </td>
                    <td class="text-center">
                        <router-link :to="{name: 'posts.edit', params: {'id': record.id}}" class="btn btn-sm btn-success">
                            <span>ویرایش</span>
                        </router-link>
                        <a class="btn btn-sm btn-warning" @click="remove(record.id)">حذف</a>
                    </td>
                </tr>
                </tbody>
            </table>
            <pagination v-bind:records="records" @pageChanged="prepareUrlAndSubmit"></pagination>
        </div>

    </div>
</template>

<script>
    import moment from 'moment';
    import Errors from "../../../includes/Errors";
    import Records from "../../../includes/DataRecords";
    import Pagination from "../../elements/pagination";
    import Notifications from "../../elements/Notifications";

    export default {
        name: "ListPost",
        components: {
            Pagination,
            Notifications
        },
        data() {
            return {
                'search':{
                    'id':'',
                    'title':'',
                    'key':'',
                    'show_status':2,
                },
                'records': new Records(),
                'base_url': '/management/api/post/list',
                'filters': {},
                'is_filtering': false,
                'errors': new Errors(),
                'message': ''
            }
        },
        methods : {
            getList(url) {
                this.records = new Records();
                window.axios.post(url, this.search)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.records.fill(response.data.data.records);
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
            prepareUrlAndSubmit(new_page, parameters)
            {
                let url = this.base_url + '?page=' + new_page;

                if (typeof parameters === typeof undefined || !parameters || Object.keys(parameters).length === 0)
                {
                    if (Object.keys(this.filters).length)
                    {
                        let _this = this;
                        Object.keys(this.filters).forEach(function(k, v){
                            if (_this.filters[k].toString().length)
                            {
                                url += '&' + k + '=' + _this.filters[k];
                            }
                        });
                    }
                }
                else
                {
                    Object.keys(parameters).forEach(function(k, v){
                        if (k !== 'page' && parameters[k].toString().length)
                        {
                            url += '&' + k + '=' + parameters[k];
                        }
                    });
                }

                this.getList(url);
            },
            remove(id) {
                let answer = confirm('آیا از حذف این رکورد اطمینان دارید؟');

                if (answer) {
                    window.axios.post('/management/api/post/delete/' + id, {})
                        .then(response => {
                            if (response.data.returnCode === 0)
                            {
                                this.message = response.data.message;
                                this.getList(this.base_url);
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
        },
        mounted() {
            this.getList(this.base_url);
        },
        filters: {
            fromNow: function (date) {
                    moment.locale('fa');
                    return moment(date).fromNow(); ;
                }
        }
    }
</script>

<style scoped>

</style>
