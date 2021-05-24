<template>
    <div>
        <div class="row">
            <div class="col-sm-7 text-right pt-2">
                <h2 class="h4 mb-4 text-info">لیست دیدگاه های محصولات</h2>
            </div>
        </div>

        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div v-if="this.records.total === 0">
            <p class="alert alert-warning">داده ای جهت نمایش وجود ندارد</p>
        </div>
        <div v-if="this.records.total > 0">
            <table class="lian-table table table-hover table-borderless border-0">
                <thead>
                <tr>
                    <th class="sm" scope="col">#</th>
                    <th class="md" scope="col">عنوان محصول</th>
                    <th class="md text-center" scope="col">نام و نام خانوادگی</th>
                    <th class="md text-center" scope="col">زمان</th>
                    <!-- <th class="md text-center" scope="col">متن</th> -->
                    <th class="md text-center" scope="col">وضعیت</th>
                    <th class="md text-center" scope="col">عملیات</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(record, index) in records.data" :key="record.id">
                    <th scope="row">{{record.id}}</th>
                    <td>
                        <router-link v-if="record.commentable" :to="{name: 'product.edit', params: {'id': record.commentable.id}}">
                            <span>{{record.commentable.title}}</span>
                        </router-link>
                    </td>
                    <td class="text-center">{{record.name}}</td>
                    <td class="text-center">{{record.created_at | fromNow}}</td>
                    <!-- <td>{{record.body}}</td> -->
                    <td class="text-center">{{record.show_status ? 'فعال' : 'غیر فعال'}}</td>
                    <td class="text-center">
                        <router-link class="btn btn-sm btn-success" :to="{name: 'comment.edit', params: {'id': record.id}}">
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
        name: "ListProductComments",
        components: {
            Pagination,
            Notifications
        },
        data() {
            return {
                'records': new Records(),
                'base_url': '/management/api/product/comments/list',
                'filters': {},
                'is_filtering': false,
                'errors': new Errors(),
                'message': ''
            }
        },
        methods : {
            getList(url) {
                window.axios.post(url, {})
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
                    window.axios.post('/management/api/product/comments/delete/' + id, {})
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
            }
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
