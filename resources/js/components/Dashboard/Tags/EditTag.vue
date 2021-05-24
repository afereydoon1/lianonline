<template>
    <div @keydown="errors.clear($event.target.name)">
        <h2 class="h4 mb-4 text-info">ویرایش برجسب</h2>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-4" :class="{'has-error' : errors.has('name')}">
                    <label class="font-15 bold-600 mb-2" for="name">عنوان برجسب</label>
                    <input type="text" v-model="fields.name" id="name" name="name" class="form-control" placeholder="">
                    <span class="error-text" v-if="errors.has('name')">{{ errors.get('name')}}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="saveTag()">ذخیره</button>
                <router-link :to="{name: 'tags.list'}" class="btn btn-secondary rounded-pill py-2 px-5 font-14">
                    <span>بازگشت</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
    import Notifications from "../../elements/Notifications";
    import Editor from '@tinymce/tinymce-vue';
    import Errors from "../../../includes/Errors";

    export default {
        name: "CreateTag",
        components : {
            'editor': Editor,
            'Notifications': Notifications,
        },
        data() {
            return {
                'fields': {
                    'id': '',
                    'name': ''
                },
                'defaultFieldsValue': {
                    'id': '',
                    'name': ''
                },
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
                'base_url': '/management/api/tag',

            };
        },
        methods : {
            getTagInfo() {
                window.axios.post(this.base_url + '/show/' + this.$route.params.id, {})
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
            saveTag() {
                this.errors.clear('general');
                this.message = '';
                window.axios.post(this.base_url + '/edit/' + this.fields.id, this.fields)
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
            this.getTagInfo();
        },
    }
</script>

<style lang="sass">

</style>
