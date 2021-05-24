<template>
    <div class="login-box">
        <div class="lian-logo-holder">
            <a>
                <img src="/images/management/logo.png" alt="lian-logo">
            </a>
        </div>
        <div class="login-form-holder">
            <div class="login-form-col">
                <div class="auth-form mx-auto" @keydown="errors.clear($event.target.name)">
                    <h2 class="auth-title ellipsis font-20 bold-900 mb-5">ورود به حساب کاربری</h2>
                    <span class="text-danger" v-if="errors.has('general')">{{ errors.get('general')}}</span>
                    <div class="form-group mb-4" :class="{'has-error' : errors.has('email')}">
                        <label class="font-15 bold-600 mb-2" for="email">آدرس ایمیل</label>
                        <input type="text" v-model="fields.email" id="email" name="email" class="form-control ltr" placeholder="آدرس ایمیل خود را وارد نمایید">
                        <span class="error-text" v-if="errors.has('email')">{{ errors.get('email')}}</span>
                    </div>
                    <div class="form-group mb-4" :class="{'has-error' : errors.has('password')}">
                        <label class="font-15 bold-600 mb-2" for="password">رمز عبور</label>
                        <input type="password" v-model="fields.password" id="password" name="password" class="form-control ltr" placeholder="رمز عبور خود را وارد نمایید">
                        <span class="error-text" v-if="errors.has('password')">{{ errors.get('password')}}</span>
<!--                        <div class="text-left">-->
<!--                            <a class="text-primary font-12 bold-600" href="forgot.html">فراموشی رمز عبور؟</a>-->
<!--                        </div>-->
                    </div>
                    <div class="text-center mb-4">
                        <button class="btn btn-secondary rounded-pill py-2 px-5 font-14" @click="doLogin()">ورود</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Errors from "../../includes/Errors";

    export default {
        name: "LoginForm.vue",

        data() {
            return {
                'fields': {
                    'email': '',
                    'password': '',
                },
                'in_progress': false,
                'errors': new Errors()
            };
        },

        methods: {
            doLogin() {
                this.in_progress = true;

                window.axios.post('/management/api/auth/login', this.fields, {timeout: 3000})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.$store.state.management.access_token = response.data.data.access_token;
                            window.localStorage.management_access_token = this.$store.state.management.access_token;
                            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.$store.state.management.access_token;
                            window.location.href = '/management/';
                        }  else
                        {
                            if(response.data.data.errors != undefined) {
                                if (Object.keys(response.data.data.errors).length)
                                {
                                    this.errors.record(response.data.data.errors);
                                }
                            }
                            else
                            {
                                this.errors.record({'general': [response.data.message]});
                            }
                        }
                        this.in_progress = false;
                    })
                    .catch(error => {
                        let data = error;

                        if (data.errors)
                        {
                            this.errors.record(data.errors);
                        } else {
                            this.errors.record(
                                {'general': ['Err:'+error.message]}
                            );
                        }


                        this.in_progress = false;
                    });
            }
        },
        mounted() {
            // this.getCaptcha();
        }
    }
</script>

<style lang="sass">
.login-box
    display: table
    width: 100%
    height: 100vh
    .lian-logo-holder
        display: table-cell
        width: 50%
        vertical-align: middle
        background: #f6f6f8
        text-align: center
    .login-form-holder
        display: table-cell
        width: 50%
        vertical-align: middle
        background: #ffffff
        .login-form-col
            max-width: 500px
            width: 100%
            display: block
            margin: auto
            padding: 15px

@media screen and (max-width: 992px)
    .login-box
        display: block
        .lian-logo-holder
            display: inline-block
            height: 25vh
            width: 100%
            padding-top: 60px
        .login-form-holder
            display: inline-block
            height: 75vh
            width: 100%
            padding-top: 40px
</style>
