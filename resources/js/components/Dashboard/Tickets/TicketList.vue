<template>
    <div>
        <div class="row">
            <div class="col-sm-7 text-right pt-2">
                <h2 class="h4 mb-4 text-info">لیست تیکتها</h2>
            </div>
            <div class="col-sm-5 text-left">
                <router-link :to="{name: 'tickets.create'}" class="btn btn-info m-1 item ">
                    <i class="fas fa-plus"></i> ثبت تیکت جدید
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
                    <th class="md">عنوان</th>
                    <th class="md text-center">کاربر</th>
                    <th class="md text-center">زمان</th>
                    <th class="md text-center">وضعیت</th>
                    <th class="md text-center">عملیات</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(record, index) in records.data" :key="index">
                    <td scope="row">{{record.id}}</td>
                    <td>{{record.title}}</td>
                    <td class="text-center">
                        <router-link  v-if="record.creator" :to="{name: 'users.edit', params: {'id': record.creator.id}}">
                            <span>{{record.creator.name ? record.creator.name : record.creator.mobile}}</span>
                        </router-link>
                    </td>
                    <td class="text-center">{{record.updated_at | fromNow}}</td>
                    <td class="text-center">{{stringifyStatus(record.status)}}</td>
                    <td class="text-center">
                        <router-link :to="{name: 'tickets.edit', params: {'id': record.id}}" class="btn btn-sm btn-success">
                            <span>مشاهده</span>
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
        name: "TicketList",
        components: {
            Pagination,
            Notifications
        },
        data() {
            return {
                'records': new Records(),
                'base_url': '/management/api/get-tickets',
                'filters': {},
                'is_filtering': false,
                'errors': new Errors(),
                'message': ''
            }
        },
        methods : {
            getList(url) {
                window.axios.post(url, {
                    'type': 'support'
                })
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
            remove(ticketID) {
                if(confirm("آیا این تیکت کاملا از سیستم پاک شود؟")) {
                    axios.post('/management/api/del-ticket',{ticket_id: ticketID})
                        .then(response => {
                                if (response.data.returnCode === 0)
                                {
                                    // this.artists.data.splice(index, 1);
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
            prepareUrlAndSubmit(new_page, parameters)
            {
                let url = this.base_url+ '/get-tickets' + '?page=' + new_page;

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
            stringifyStatus(data) {
                switch(data) {
                    case "new":
                        return 'باز';
                        break;
                    case "answered":
                        return 'پاسخ داده شده';
                        break;
                    case "processing":
                        return 'در حال انجام';
                        break;
                    case "closed":
                        return 'بسته شده';
                        break;
                    default:
                        return data;//'باز';
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
