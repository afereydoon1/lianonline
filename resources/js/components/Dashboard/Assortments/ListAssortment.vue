<template>
    <div>
        <div class="row">
            <div class="col-sm-7 text-right pt-2">
                <h2 class="h4 mb-4 text-info">لیست دسته بندی ها</h2>
            </div>
            <div class="col-sm-5 text-left">
                <router-link :to="{name: 'assortment.create'}" class="btn btn-info m-1 item ">
                <i class="fas fa-plus"></i> افزودن دسته بندی جدید
                </router-link>
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
                    <th class="md" scope="col">عنوان دسته بندی</th>
                    <th class="md" scope="col">دسته بندی والد</th>
                    <th class="md text-center" scope="col">ترتیب نمایش</th>
                    <th class="md text-center" scope="col">عملیات</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(record, index) in records.data">
                    <th scope="row">{{index+1}}</th>
                    <td>{{record.title}}</td>
                    <td>{{record.parent ? record.parent.title : '---'}}</td>
                    <td class="text-center">{{record.order}}</td>
                    <td class="text-center">
                        <router-link :to="{name: 'assortment.edit', params: {'id': record.id}}" class="btn btn-sm btn-success">
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
    import Errors from "../../../includes/Errors";
    import Records from "../../../includes/DataRecords";
    import Pagination from "../../elements/pagination";
    import Notifications from "../../elements/Notifications";

    export default {
        name: "ListAssortment",
        components: {
            Pagination,
            Notifications
        },
        data() {
            return {
                'records': new Records(),
                'base_url': '/management/api/assortment/list',
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
                    window.axios.post('/management/api/assortment/delete/' + id, {})
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
        }
    }
</script>

<style scoped>

</style>
