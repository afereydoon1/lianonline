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
                    <input type="password" name="old_password" class="msh-control-input" id="old_password" placeholder=" " v-model="record.old_password">
                    <label class="msh-control-label" for="old_password">رمزعبور فعلی</label>
                </div>
                <span class="error-text" v-if="errors.has('old_password')">{{ errors.get('old_password')}}</span>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="msh-control mb-0">
                    <input type="password" name="password" class="msh-control-input" id="password" placeholder=" " v-model="record.password">
                    <label class="msh-control-label" for="password">رمزعبور</label>
                </div>
                <span class="error-text" v-if="errors.has('password')">{{ errors.get('password')}}</span>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="msh-control mb-0">
                    <input type="password" name="password_confirmation" class="msh-control-input" id="password_confirmation" placeholder=" " v-model="record.password_confirmation">
                    <label class="msh-control-label" for="password_confirmation">تکرار رمزعبور</label>
                    <span class="error-text" v-if="errors.has('password_confirmation')">{{ errors.get('password_confirmation')}}</span>
                </div>
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
        name: "Password",
        data() {
            return {
                record: {
                    old_password: '',
                    password: '',
                    password_confirmation: '',
                },
                in_progress: false,
                base_url: '/panel/api',
                message: '',
                errors: new Errors()
            }
        },
        methods: {
            update() {
                this.in_progress = true;
                let url = this.base_url + '/password/edit';
                axios.post(url, this.record, {timeout: 3000})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.message = response.data.message;
                            this.record = {
                                old_password: '',
                                password: '',
                                password_confirmation: ''
                            };
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
                this.message = '';
                this.errors = new Errors();
                this.record = {
                    old_password: '',
                    password: '',
                    password_confirmation: ''
                };
            }
        }
    }
</script>

<style scoped>

</style>
