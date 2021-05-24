<template>
    <div>
        <div class="row">
            <div class="col-sm-7 text-right pt-2">
                <h2 class="h4 mb-4 text-info">اسلایدر</h2>
            </div>
        </div>
        <notifications :error="errors.get('general')" :message="message"></notifications>
        <div class="row mt-5">
            <div class="col-12">
                <label class="font-15 bold-600 mb-2">تصاویر : </label>
                <uploader ref="uploader" size="1000000"
                :upload_path="base_url + '/settings/slides/upload'"
                :images="images?images:[]"
                :delete_path="base_url + '/settings/slides/delete/'"
                @filesUpdated="filesUpdated"></uploader>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
                <button class="btn btn-success rounded-pill py-2 px-5 font-14" @click="saveList()">ذخیره</button>
            </div>
        </div>

    </div>
</template>

<script>
    import moment from 'moment';
    import Errors from "../../../includes/Errors";
    import Records from "../../../includes/DataRecords";
    import Pagination from "../../elements/pagination";
    import Notifications from "../../elements/Notifications";
    import Uploader from "../../elements/Files/Uploader";

    export default {
        name: "Slider",
        components: {
            Pagination,
            Notifications,
            Uploader
        },
        data() {
            return {
                'records': new Records(),
                'base_url': '/management/api',
                'filters': {},
                'images': [],
                'is_filtering': false,
                'errors': new Errors(),
                'message': ''
            }
        },
        methods : {
            filesUpdated(images) {
                this.images = [];
                if(images.length) {
                    this.images = [...images];
                }
            },
            getList(url) {
                window.axios.post(this.base_url+'/settings/slides/list', {})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.images = response.data.data.slide;
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
            saveList() {
                let data = {};
                data['images'] = [];
                for (let i = 0; i < this.images.length; i++) {
                    if(this.images[i].download_path != undefined) {
                        data['images'].push(this.images[i].id);
                    }else{
                        if(this.images[i].success) {
                            data['images'].push(this.images[i].file_id);
                        }
                    }
                }
                window.axios.post(this.base_url+'/settings/slides/set', data)
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.getList()
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
