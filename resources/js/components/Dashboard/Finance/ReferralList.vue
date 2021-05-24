<template>
    <div>
        <div class="row">
            <div class="col-sm-7 text-right pt-2">
                <h2 class="h4 mb-4 text-info">گزارش بازاریابی</h2>
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
                    <th class="md text-center" scope="col">نام کاربر</th>
                    <th class="md text-center" scope="col">صفحه فرود</th>
                    <th class="md text-center" scope="col">صفحه فرستنده</th>
                    <th class="md text-center" scope="col">IP</th>
                    <th class="md text-center" scope="col">وضعیت</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(record, index) in records.data" :key="record.id">
                    <th scope="row">{{record.id}}</th>
                    <td class="text-center">
                        <span v-if="record.referral_link">
                            <router-link v-if="record.referral_link.user" :to="{name: 'users.edit', params: {'id': record.referral_link.user.id}}">
                                <span>{{record.referral_link.user.name}}</span>
                            </router-link>
                        </span>
                    </td>
                    <td class="text-center">{{record.landing}}</td>
                    <td class="text-center">{{record.host}}</td>
                    <td class="text-center">{{record.ip}}</td>
                    <td class="text-center">{{record.order ? record.order.order_status : 'عدم وجود سفارش'}}</td>
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
        name: "ReferralList",
        components: {
            Pagination,
            Notifications
        },
        data() {
            return {
                'records': new Records(),
                'base_url': '/management/api/referral/list',
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
