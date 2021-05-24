<template>
    <div class="mb-4">
        <nav aria-label="Page navigation" v-if="records.last_page > 1">
            <ul class="pagination justify-content-center">
                <li class="page-item" :class="{'disabled' : records.current_page === 1}" :disabled="records.current_page === 1">
                    <a class="page-link" @click="changePage(records.current_page - 1)" :disabled="records.current_page === 1">صفحه قبل</a>
                </li>
                <li class="page-item" :class="{'active' : records.current_page === page}" v-for="page in pages">
                    <a v-if="records.current_page !== page" class="page-link" @click="changePage(page)">{{page}}</a>
                    <span class="page-link" v-if="records.current_page === page">صفحه {{page}}  از  {{records.last_page}}</span>
                </li>
                <li class="page-item" :class="{'disabled' : records.current_page === records.last_page}" :disabled="records.current_page === records.last_page">
                    <a class="page-link" @click="changePage(records.current_page + 1)" :disabled="records.current_page === records.last_page">صفحه بعد</a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    export default {
        name: "pagination",
        props: [
            'records'
        ],
        data(){
            return {

            }
        },
        computed: {
            startPage() {
                if (this.records.current_page > 3 && this.records.last_page > 5)
                {
                    if (this.records.current_page >= this.records.last_page - 2)
                    {
                        return this.records.last_page - 4;
                    }
                    else
                    {
                        return this.records.current_page - 2;
                    }
                }

                return 1;
            },
            endPage() {
                if (this.records.last_page > 5)
                {
                    if (this.records.current_page > 3)
                    {
                        if (this.records.current_page < this.records.last_page - 2)
                        {
                            return this.records.current_page + 2;
                        }
                        else
                        {
                            return this.records.last_page;
                        }
                    }
                    else
                    {
                        return 5;
                    }
                }

                return this.records.last_page;
            },
            pages() {
                let pages = [];
                for (let i = this.startPage; i <= this.endPage; i++)
                {
                    pages.push(i);
                }

                return pages;
            }
        },
        methods: {
            changePage(page)
            {
                if (page !== this.records.current_page)
                {
                    this.$emit('pageChanged', page)
                }
            }
        }
    }
</script>

<style lang="sass">
ul.pagination
    li
        &:not(.active)
            .page-link
                &:not(:disabled)
                    cursor: pointer
</style>
