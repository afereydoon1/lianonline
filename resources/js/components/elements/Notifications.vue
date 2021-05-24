<template>
    <div class="row">
        <div class="col-12">
            <p class="alert alert-danger" v-if="error">{{error}}</p>
            <p class="alert alert-success" v-if="message">{{message}}</p>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Notifications',
        props: [
            'error',
            'message'
        ]
    };
</script>
