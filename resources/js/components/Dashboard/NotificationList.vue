<template>
    <div>
        <div class="row">
            <div class="col-sm-7 text-right pt-2">
                <h2 class="h4 mb-4 text-info">لیست اطلاعیه ها</h2>
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
                    <!-- <th class="md">عنوان</th> -->
                    <th class="md text-center">عنوان</th>
                    <th class="md text-center">کاربر</th>
                    <th class="md text-center">مرتبط</th>
                    <th class="md text-center">زمان</th>
                    <th class="md text-center">عملیات</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(record, index) in records.data" :key="index">
                    <td scope="row">{{record.id}}</td>
                    <!-- <td>{{record.text}}</td> -->
                    <td>{{types[record.type]}}</td>
                    <td class="text-center">
                        <router-link v-if="record.user" :to="{name: 'users.edit', params: {'id': record.user.id}}">
                            <span>{{record.user.name ? record.user.name : record.user.mobile}}</span>
                        </router-link>
                    </td>
                    <td class="text-center">
                        <router-link v-if="record.connect" :to="{name: stringifyRoute(record.type), params: {'id': record.connect.id}}">
                            <span>{{stringifyConnect(record.type)}}</span>
                        </router-link>
                    </td>
                    <td class="text-center">{{record.created_at | fromNow}}</td>
                    <td class="text-center">
                        <a v-if="!record.read" class="btn btn-sm btn-warning" @click="unread(record.id)">خوانده شده</a>
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
    import Errors from "../../includes/Errors";
    import Records from "../../includes/DataRecords";
    import Pagination from "../../components/elements/pagination";
    import Notifications from "../../components/elements/Notifications";

    export default {
        name: "NotificationList",
        components: {
            Pagination,
            Notifications
        },
        data() {
            return {
                'records': new Records(),
                'base_url': '/management/api/notification/list',
                'filters': {},
                'is_filtering': false,
                'errors': new Errors(),
                'message': '',
                'types' : {
                    'CreateUser': 'ثبت کاربر جدید',
                    'EditUser': 'ویرایش کاربر',
                    'DelUser': 'حذف کاربر',
                    'CreatePost': 'ثبت پست جدید',
                    'EditPost': 'ویرایش پست',
                    'DelPost': 'حذف پست',
                    'CreatePostComment': 'ثبت کامنت جدید یرای پست',
                    'CreateProduct': 'ثبت محصول جدید',
                    'EditProduct': 'ویرایش محصول',
                    'DelProduct': 'حذف محصول',
                    'CreateProductComment': 'ثبت کامنت جدید یرای محصول',
                    'CreateTicket': 'ثبت تیکت جدید',
                    'ReplayTicket': 'پاسخ به تیکت',
                    'CopyRightTicket': 'گزارش نقض کپی رایت',
                    'AbuseReportTicket': 'گزارش تخلف',
                    'CreateOrder': 'ثبت سفارش جدید',
                    'OrderSuccess': 'ثبت سفارش موفق',
                    'CreateWithdrawal': 'ثبت درخواست وجه',
                }
            }
        },
        methods : {
            getList(url) {
                window.axios.post(url, {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.records.fill(response.data.data.records);
                            this.$store.state.management.unreadNotification = response.data.data.unReadNotification;
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
            unread(ticketID) {
                    axios.post('/management/api/notification/read/'+ticketID)
                        .then(response => {
                                if (response.data.returnCode === 0)
                                {
                                    this.$store.state.management.unreadNotification = response.data.data.unReadNotification;
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
            stringifyConnect(data) {
                switch(data) {
                    case "CreateUser":
                    case "EditUser":
                    case "DelUser":
                        return 'کاربر';
                        break;
                    case "CreatePost":
                    case "EditPost":
                    case "DelPost":
                        return 'پست';
                        break;
                    case "CreateProduct":
                    case "EditProduct":
                    case "DelProduct":
                        return 'محصول';
                        break;
                    case "CreateTicket":
                    case "ReplayTicket":
                        return 'تیکت';
                        break;
                    case "CreateOrder":
                    case "OrderSuccess":
                        return 'سفارش';
                        break;
                    case "CreateWithdrawal":
                        return 'درخواست وجه';
                        break;
                    case "CreatePostComment":
                        return '';
                        break;
                    case "CreateProductComment":
                        return '';
                        break;
                    case 'CopyRightTicket':
                        return 'گزارش';
                        break;
                    case 'AbuseReportTicket':
                        return 'گزارش';
                        break
                    default:
                        return data;
                }
            },
            stringifyRoute(data) {
                switch(data) {
                    case "CreateUser":
                    case "EditUser":
                    case "DelUser":
                        return 'users.edit';
                        break;
                    case "CreatePost":
                    case "EditPost":
                    case "DelPost":
                        return 'posts.edit';
                        break;
                    case "CreateProduct":
                    case "EditProduct":
                    case "DelProduct":
                        return 'product.edit';
                        break;
                    case "CreateTicket":
                    case "ReplayTicket":
                    case 'CopyRightTicket':
                    case 'AbuseReportTicket':
                        return 'tickets.edit';
                        break;
                    case "CreateOrder":
                    case "OrderSuccess":
                        return 'orders.show';
                        break;
                    case "CreateWithdrawal":
                        return 'finance.checkout.edit';
                        break;
                    case "CreatePostComment":
                        return 'posts.comments';
                        break;
                    case "CreateProductComment":
                        return 'product.comments';
                        break;
                    default:
                        return data;
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
