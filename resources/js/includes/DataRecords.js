export default class Records {
    constructor() {
        this.current_page = 1;
        this.data = {};
        this.first_page_url = 1;
        this.from = 1;
        this.last_page = 1;
        this.last_page_url = '';
        this.next_page_url = '';
        this.path = '';
        this.per_page = 20;
        this.prev_page_url = '';
        this.to = 1;
        this.total = 0;
    }

    fill(data) {
        this.current_page = data.current_page;
        this.data = data.data;
        this.first_page_url = data.first_page_url;
        this.from = data.from;
        this.last_page = data.last_page;
        this.last_page_url = data.last_page_url;
        this.next_page_url = data.next_page_url;
        this.path = data.path;
        this.per_page = data.per_page;
        this.prev_page_url = data.prev_page_url;
        this.to = data.to;
        this.total = data.total;
    }
}
