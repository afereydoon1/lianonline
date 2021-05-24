<template>
    <div>
        <div class="col-12 mb-5" >
            <canvas id="canvas" style="display: block; width: 100vw; height: 60vh;" ref=mainChart></canvas>
        </div>
        <!--<div class="col-12 chart" >
            <line-chart v-if="datacollection" class="col-12 px-2 py-2" :chart-data="datacollection" :options="options"></line-chart>
        </div>-->
        <div class="col-12">
            <div class="stats-widget clearfix">
                <div class="row no-gutters mx-n2">
                    <div class="col-6 col-sm-4 col-lg-2 px-2">
                        <a href="#" class="stat-item d-block bg-light py-4 px-2 mb-3 rounded transition text-center clearfix">
                            <span class="i d-block text-secondary fas fa-sticky-note"></span>
                            <span class="t ellipsis my-2 text-secondary font-10 bold-400">نوشته</span>
                            <span class="v ellipsis text-dark font-24 bold-900">{{fields.total_posts}}</span>
                        </a>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2 px-2">
                        <a href="#" class="stat-item d-block bg-light py-4 px-2 mb-3 rounded transition text-center clearfix">
                            <span class="i d-block text-secondary fas fa-shopping-cart"></span>
                            <span class="t ellipsis my-2 text-secondary font-10 bold-400">محصول</span>
                            <span class="v ellipsis text-dark font-24 bold-900">{{fields.total_products}}</span>
                        </a>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2 px-2">
                        <a href="#" class="stat-item d-block bg-light py-4 px-2 mb-3 rounded transition text-center clearfix">
                            <span class="i d-block text-secondary fas fa-user"></span>
                            <span class="t ellipsis my-2 text-secondary font-10 bold-400">کاربر</span>
                            <span class="v ellipsis text-dark font-24 bold-900">{{fields.total_users}}</span>
                        </a>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2 px-2">
                        <a href="#" class="stat-item d-block bg-light py-4 px-2 mb-3 rounded transition text-center clearfix">
                            <span class="i d-block text-secondary fas fa-shopping-bag"></span>
                            <span class="t ellipsis my-2 text-secondary font-10 bold-400">فروش</span>
                            <span class="v ellipsis text-dark font-24 bold-900">{{fields.success_orders}}</span>
                        </a>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2 px-2">
                        <a href="#" class="stat-item d-block bg-light py-4 px-2 mb-3 rounded transition text-center clearfix">
                            <span class="i d-block text-secondary fas fa-comment-alt"></span>
                            <span class="t ellipsis my-2 text-secondary font-10 bold-400">دیدگاه</span>
                            <span class="v ellipsis text-dark font-24 bold-900">{{fields.total_comments}}</span>
                        </a>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2 px-2">
                        <a href="#" class="stat-item d-block bg-light py-4 px-2 mb-3 rounded transition text-center clearfix">
                            <span class="i d-block text-secondary fas fa-ticket-alt"></span>
                            <span class="t ellipsis my-2 text-secondary font-10 bold-400">تیکت</span>
                            <span class="v ellipsis text-dark font-24 bold-900">{{fields.total_tickets}}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import LineChart from '../elements/LineChart';
    import Notifications from "..//elements/Notifications";
    import Errors from "../../includes/Errors";
    var moment = require('jalali-moment');
    moment.locale('fa', { useGregorianParser: true });

    import Chart from 'chart.js';
    Chart.defaults.global.defaultFontFamily = 'iranyekan';

    export default {
        name: "Dashboard.vue",
        components : {
            Notifications,
            LineChart,
        },
        data() {
            return {
                'fields' : {
                    'chart_info': null,

                    'total_products': 0,
                    'total_posts': 0,
                    'total_orders': 0,
                    'success_orders': 0,
                    'total_tickets': 0,
                    'total_comments': 0,
                    'total_users': 0,
                },
                'datacollection':null,
                'options':{
                    responsive: true,
                    maintainAspectRatio:false,
                    title: {
                        display: false,
                        text: 'فروش ها'
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: false,
                                labelString: 'تاریخ'
                            },
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: false,
                                labelString: 'مبلغ'
                            },
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
		        },

                'base_url': '/management/api/getDashboard',
                'in_progress': false,
                'errors': new Errors(),
                'message': '',
            }
        },
        watch: {
        },
        mounted() {
            this.$store.state.breadcrumb = [];
            this.$store.state.is_route_loading = false;
            this.getInfo();
        },
        methods:{
            getInfo() {
                window.axios.post(this.base_url , {})
                    .then(response => {

                        if (response.data.returnCode === 0)
                        {
                            this.fields = response.data.data.records;
                            if(this.fields.chart_info) {

                                const labels = this.fields.chart_info.map(item => moment(item.time).format('MM/DD'))
                                const sale = this.fields.chart_info.map(item => item.sale_price/1000)
                                const siteCommission = this.fields.chart_info.map(item => item.site_commission/1000)
                                const datasets =  [{
                                    // label: 'فروش',
                                    // backgroundColor: '#3452eb',
                                    label: "فروش (هزارتومان)",
                                    backgroundColor: "#fa600b",
                                    borderColor: "#fa600b",
                                    fill: false,
                                    borderDash: [0],
                                    data: sale
                                }, {
                                    // label: 'درآمد',
                                    // backgroundColor: '#eb3456',
                                    label: "درآمد (هزارتومان)",
                                    backgroundColor: "#00c0ff",
                                    borderColor: "#00c0ff",
                                    fill: false,
                                    borderDash: [0],
                                    data: siteCommission,
                                }];
                                this.datacollection = {
                                    'labels': labels,
                                    'datasets':datasets,
                                    // scales: {
                                    //     yAxes: [{
                                    //         ticks: {
                                    //             beginAtZero: true
                                    //         }
                                    //     }]
                                    // }
                                }
                                const myChart = new Chart(this.$refs.mainChart, {
                                    'type': 'line',
                                    'data':this.datacollection,
                                    'options':this.options
                                });
                            }
                        }
                        else
                        {
                            this.assortments = [];
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
            }
        }
    }
</script>

<style scoped>
.stats-widget .stat-item .i {
    font-size: 40px
}
</style>

<style>
.select2-container--default .select2-selection--multiple {
    background-color: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #e4e4e4 !important;
    border: 1px solid #aaa !important;
    border-radius: 4px !important;
    float: right !important;
    margin: 0 0 5px 5px !important;
}

.select2-container .select2-search--inline {
    float: right !important;
}

.select2-container--default .select2-search--inline .select2-search__field:focus {
  outline: 0 !important;
  -webkit-box-shadow: none !important;
  box-shadow: none !important;
}
</style>
