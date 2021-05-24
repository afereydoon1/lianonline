<template>
    <div class="container-fluid pt-3" @keydown="errors.clear($event.target.name)">
        <div class="row mt-3" v-if="errors.has('general')">
            <div class="col-12">
                <p class="alert alert-danger">{{errors.get('general')}}</p>
            </div>
        </div>
        <div class="row mt-3" v-if="message.length">
            <div class="col-12">
                <p class="alert alert-success">{{message}}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="msh-control mb-0">
                    <input class="msh-control-input" name="name" id="user_name" placeholder=" " v-model="record.name">
                    <label class="msh-control-label" for="user_name">نام</label>
                </div>
                <span class="error-text" v-if="errors.has('name')">{{ errors.get('name')}}</span>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="msh-control mb-0">
                    <input class="msh-control-input ltr" name="email" id="user_email" placeholder=" " v-model="record.email">
                    <label class="msh-control-label" for="user_email">ایمیل</label>
                </div>
                <span class="error-text" v-if="errors.has('email')">{{ errors.get('email')}}</span>
            </div>
        </div>
        <div class="row mb-2 mt-4">
            <div class="col-12">
                <hr>
                <button class="btn btn-info" :disabled="in_progress" @click="update">
                    <img class="loading-btn" v-if="in_progress" src="images/loading.svg">
                    ذخیره تغییرات
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import Errors from "../../../../includes/Errors";
    import axios from "axios";

    export default {
        name: "Profile",
        data() {
            return {
                record: {
                    name: '',
                    email: '',
                },
                in_progress: false,
                base_url: '/panel/api',
                message: '',
                errors: new Errors()
            }
        },
        methods: {
            getUserData() {
                let url = this.base_url + '/user/info';
                axios.post(url, {}, {timeout: 3000})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.record = response.data.data;
                        }
                        this.$store.state.is_route_loading = false;
                        this.in_progress = false;
                    })
                    .catch(error => {
                        this.errors.handle(this, error);
                    });
            },
            update() {
                this.in_progress = true;
                let url = this.base_url + '/profile/edit';
                axios.post(url, this.record, {timeout: 3000})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
                        }
                        this.$store.state.is_route_loading = false;
                        this.in_progress = false;
                    })
                    .catch(error => {
                        this.errors.handle(this, error);
                    });
            },
            reset()
            {
                this.getUserData();
                this.message = '';
                this.errors = new Errors();
            }
        },
        mounted() {
            this.reset();
        }
    }
</script>

<style scoped>

</style>
