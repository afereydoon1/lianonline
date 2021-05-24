<template>
    <div>
        در حال خروج از ناحیه کاربری ...
    </div>
</template>

<script>
    export default {
        name: "Logout",
        mounted() {
            window.axios.post('/management/api/auth/logout', {}, {timeout: 1500})
                .then(response => {
                    if (response.data.returnCode === 0)
                    {
                        window.localStorage.management_access_token = null;
                        window.axios.defaults.headers.common['Authorization'] = null;
                        window.location.href = '/management/login';
                    }
                    // else
                    // {
                    //     // this.errors.record([{'general': response.data.message}]);
                    //     let data = response.data.data;
                    //     if (data.errors)
                    //     {
                    //         this.errors.record(data.errors);
                    //     } else {
                    //         this.errors.record(
                    //             {'general': [response.data.message]}
                    //         );
                    //     }
                    // }
                })
                .catch(error => {
                    this.$store.state.management.user = null;
                    window.localStorage.management_access_token = null;
                    window.axios.defaults.headers.common['Authorization'] = null;
                    window.location.href = '/management/login';
                });
        }
    }
</script>

<style scoped>

</style>
