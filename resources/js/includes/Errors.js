export default class Errors {
    constructor() {
        this.errors = {}
    }

    has(field) {
        return this.errors.hasOwnProperty(field);
    }

    get(field) {
        if (this.errors[field])
        {
            return this.errors[field][0];
        }
    }

    record(errors) {
        this.errors = errors;
    }

    reset() {
        this.errors = {};
    }

    clear(field) {
        delete this.errors['general'];
        delete this.errors[field];
    }

    any() {
        return Object.keys(this.errors).length > 0;
    }

    handle(instance, error) {
        let data = error.response.data.data;

        if (typeof data.errors !== typeof undefined && Object.keys(data.errors).length)
        {
            instance.errors.record(data.errors);
        }
        else if (error.response.data.message)
        {
            instance.errors.record({'general': [error.response.data.message]});
        }

        instance.in_progress = false;

        instance.$store.state.is_route_loading = false;
    }
}
