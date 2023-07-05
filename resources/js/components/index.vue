<template>
    <div id="dashboard-owner">
        <v-row justify="center" class="mb-1">
            <v-col class="my-1 py-0 ">
                <v-card :loading="loading">
                    <v-card-title>
                    <span class="">ข้อมูลย้อนหลัง</span>
                    </v-card-title>

                    <v-card-text>
                        <v-select
                            v-model="select_stall_number"
                            :items="stall_list"
                            label="เลือกบูธ"
                            dense
                            required
                        />

                        <line-chart
                            class="pt-2"
                            v-if="false"
                            :chartdata="chart_data"
                            :options="options"
                        />



                        <table>
                            <tr><td>วันที่</td><td class="pl-2 text-center">ยอดขาย</td><td class="pl-2 text-center">จำนวน</td></tr>
                            <tr v-for="sale in stall_sale_list" :key="sale.sale_date">
                                <td><b>{{ sale.sale_date }}</b></td>
                                <td class="pl-2 text-end">{{ sale.sale_total }}</td>
                                <td class="pl-2 text-end">{{ sale.sale_count }}</td>
                            </tr>
                        </table>


                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>

import LineChart from './chart.vue'
export default {
    components: { LineChart },
    data: () => ({
        loading: true,
        select_stall_number: '',
        stall_list: [],

        stall_sale_list: [],

        headers: [
            { text: 'วันที่', value: 'sale_date', align: 'center', sortable: false},
            { text: 'ยอด', value: 'sale_total', align: 'start' , sortable: false},
            { text: 'จำนวน', value: 'sale_count', align: 'center' , sortable: false},
        ],
        dataLoaded: false,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                labels: {
                    // This more specific font property overrides the global property
                    fontFamily: "Kanit"
                }
            },
            scales: {
                xAxes: [{
                    ticks: {
                        fontFamily: "Kanit",
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        suggestedMax: 50,
                        stepSize: 10,
                        reverse: false,
                        beginAtZero: true,
                        fontFamily: "Kanit",
                    }
                }],
            },
        },
        chart_data: {},

    }),

    computed: {
    },

    watch: {
        select_stall_number(){
            this.getStallSales()
        }
    },

    created () {
        this.loading = false
        this.initialize()
    },

    methods: {
        initialize(){
            this.loading = true

            axios
            .get('/api/stalls').then(response => {
                if (response.data.success == true) {
                    this.stall_list = response.data.stall_list
                }
            })
            .catch(error => {
                console.log("getting data error");
            });
            this.loading = false
        },

        getStallSales(){
            this.loading = true

            console.log("getting stall sales");


            axios
            .get('/api/stall/sales', {params:{stall_number:this.select_stall_number}}).then(response => {
                if (response.data.success == true) {
                    this.stall_sale_list = []
                    this.stall_sale_list = response.data.stall_sale_list
                    // console.log(this.stall_sale_list)
                }
            })
            .catch(error => {
                console.log("getting data error");
            });
            this.loading = false
        },


    },
};
</script>
