<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">تنظیمات پیامک</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('api_key')}">
                    <label class="font-15 bold-600 mb-2" for="api_key">api_key</label>
                    <input type="text" v-model="fields.api_key" id="api_key" name="api_key" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('api_key')">{{ errors.get('api_key')}}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('api_secret')}">
                    <label class="font-15 bold-600 mb-2" for="api_secret">api_secret</label>
                    <input type="text" v-model="fields.api_secret" id="api_secret" name="api_secret" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('api_secret')">{{ errors.get('api_secret')}}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('api_number')}">
                    <label class="font-15 bold-600 mb-2" for="api_number">api_number</label>
                    <input type="text" v-model="fields.api_number" id="api_number" name="api_number" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('api_number')">{{ errors.get('api_number')}}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('api_url')}">
                    <label class="font-15 bold-600 mb-2" for="api_url">api_url</label>
                    <input type="text" v-model="fields.api_url" id="api_url" name="api_url" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('api_url')">{{ errors.get('api_url')}}</span>
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
        name: "Sms",
        components : {
            'Notifications': Notifications
        },
        data() {
            return {
                'fields': {
                    'api_key': '',
                    'api_secret': '',
                    'api_number': '',
                    'api_url': ''
                },
                'defaultFieldsValue': {
                    'api_key': '',
                    'api_secret': '',
                    'api_number': '',
                    'api_url': ''
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/settings',

            };
        },
        methods : {
            getSetting() {
                window.axios.post(this.base_url + '/sms', {})
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
                window.axios.post(this.base_url + '/sms/save', this.fields)
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
