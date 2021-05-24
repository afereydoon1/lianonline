<template>
    <div>
        <div class="row">
            <div class="col-sm-7 text-right pt-2">
                <h2 class="h4 mb-4 text-info">لیست تراکنش ها</h2>
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
                    <th class="md text-center" scope="col">عنوان</th>
                    <th class="md text-center" scope="col">نوع</th>
                    <th class="md text-center" scope="col">مبلغ</th>
                    <th class="md text-center" scope="col">محصول</th>
                    <th class="md text-center" scope="col">کاربر</th>
                    <th class="md text-center" scope="col">زمان</th>
                    <th class="md text-center" scope="col">وضعیت</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(record, index) in records.data" :key="record.id">
                    <th scope="row">{{record.id}}</th>
                    <td class="text-center">{{record.text}}</td>
                    <td class="text-center">{{stringifyTypes(record.type)}}</td>
                    <td class="text-center">{{record.amount}}</td>
                    <td class="text-center">
                        <router-link v-if="record.product" :to="{name: 'product.edit', params: {'id': record.product.id}}">
                            <span>{{record.product.title}}</span>
                        </router-link>
                        <span v-else>---</span>
                    </td>
                    <td class="text-center">
                        <router-link v-if="record.user"  :to="{name: 'users.edit', params: {'id': record.user.id}}">
                            <span>{{record.user.name}}</span>
                        </router-link>
                    </td>
                    <td class="text-center">{{record.created_at | fromNow}}</td>
                    <td class="text-center">{{stringifyStatus(record.status)}}</td>
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
        name: "ListTransactions",
        components: {
            Pagination,
            Notifications
        },
        data() {
            return {
                'records': new Records(),
                'base_url': '/management/api/transactions/list',
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
            stringifyTypes(data) {
                switch(data) {
                    case "sale":
                        return 'فروش';
                        break;
                    case "referral":
                        return 'بازاریابی';
                        break;
                    case "site":
                        return 'سایت';
                        break;
                    case "charge":
                        return 'شارژ';
                        break;
                    case "purchase":
                        return 'پرداخت';
                        break;
                    case "penalty":
                        return 'کسر از حساب';
                        break;
                    case "withdrawal":
                        return 'درخواست وجه';
                        break;
                    default:
                        return data;
                    }
            },
            stringifyStatus(data) {
                switch(data) {
                    case "success":
                        return 'موفق';
                        break;
                    case "failed":
                        return 'ناموفق';
                        break;
                    case "pending":
                        return 'در انتظار';
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
