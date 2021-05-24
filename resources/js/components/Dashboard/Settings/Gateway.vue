<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">تنظیمات بانک</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('merchantId')}">
                    <label class="font-15 bold-600 mb-2" for="merchantId">merchantId</label>
                    <input type="text" v-model="fields.merchantId" id="merchantId" name="merchantId" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('merchantId')">{{ errors.get('merchantId')}}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('min_withdrawal_amount')}">
                    <label class="font-15 bold-600 mb-2" for="min_withdrawal_amount">حداقل درخواست وجه</label>
                    <input type="number" v-model="fields.min_withdrawal_amount" id="min_withdrawal_amount" name="min_withdrawal_amount" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('min_withdrawal_amount')">{{ errors.get('min_withdrawal_amount')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="saveSetting()">ذخیره</button>
                <router-link :to="{name: 'dashboard'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
                    <span>انصراف</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
    import Notifications from "../../elements/Notifications";
    import Errors from "../../../includes/Errors";

    export default {
        name: "Gateway",
        components : {
            'Notifications': Notifications
        },
        data() {
            return {
                'fields': {
                    'merchantId': '',
                    'min_withdrawal_amount':0,
                },
                'defaultFieldsValue': {
                    'merchantId': '',
                    'min_withdrawal_amount':0,
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/settings',

            };
        },
        methods : {
            getSetting() {
                window.axios.post(this.base_url + '/gateway', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
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
            saveSetting() {
                this.errors.clear('general');
                this.message = '';
                window.axios.post(this.base_url + '/gateway/save', this.fields)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
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
        },
        mounted() {
            this.getSetting();
        },
    }
</script>

<style lang="sass">

</style>
