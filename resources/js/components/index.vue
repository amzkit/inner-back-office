<template>
    <div class="container">
        <v-row justify="center" class="pl-2 pr-2 mb-1">
            <v-col class="my-1 py-0 ">
                <v-card :loading="loading">
                    <v-card-title>
                    <span class="">ข้อมูลย้อนหลัง</span>
                    </v-card-title>

                    <v-card-text>
                        <v-select
                            v-model="select_team"
                            :items="team_list"
                            item-text="name"
                            label="เลือกเชฟ"
                            dense
                            required
                        />
                        <div class="">
                            <v-table                            
                                fixed-header
                                density="compact"
                                v-if="team"
                                class="mb-5"
                            >
                                <thead>
                                <tr><th><b>{{ team.name }}</b></th>
                                    <template v-for="(stall_sale_total_sum, index) in team_sale_list.team_sale_total_sum" :key="stall_sale_total_sum+index">
                                        <th class="pl-2 text-center">{{ stall_sale_total_sum }}</th>
                                    </template>
                                </tr>
                                <tr>
                                    <th class="pl-2 text-end"></th>
                                    <template v-for="(stall_number, index) in team_sale_list.header" :key="stall_number+index">
                                        <th class="pl-2 text-center">{{ stall_number }}</th>
                                    </template>
                                </tr>
                                </thead>


                                <template v-for="(sales, date) in team_sale_list.sales_by_date" :key="date+index">
                                    <tr>
                                        <td><b>{{ date }}</b></td>
                                        <template v-for="(sale, index) in sales" :key="date+sale+index">
                                            <template v-if="date == 'รวม' || index == sales.length -1 ">
                                                <td class="pl-2 text-end font-weight-bold">{{ sale }}</td>
                                            </template>
                                            <template v-else>
                                                <td class="pl-2 text-end">{{ sale }}</td>
                                            </template>
                                        </template>
                                    </tr>
                                </template>

                                <template v-for="(stall) in team_sale_list" :key="stall.stall_number">
                                    <tr v-for="(sale, sale_index) in stall.sales" :key="stall.stall_number+sale.sale_date+sale.sale_date+sale.sale_total+sale.sale_count">
                                        <td><template v-if="sale_index==0"><b>{{ stall.stall_number }}</b></template></td>
                                        <td class="pl-2 text-end"><b>{{ sale.sale_date }}</b></td>
                                        <td class="pl-2 text-end">{{ sale.sale_total }}</td>
                                        <td class="pl-2 text-end">{{ sale.sale_count }}</td>
                                    </tr>
                                </template>
                                
                            </v-table>
                        </div>

                        
                        <table v-if="team_sale_list.length && false" class="mb-3">
                            <tr>
                            <th></th><th class="pl-2 text-end">วันที่</th><th class="pl-2 text-center">ยอดขาย</th><th class="pl-2 text-center">จำนวน</th>
                            </tr>
                            <tr><td><b>{{ team.name }}</b></td>
                                <td class="pl-2 text-end font-weight-bold">รวมทุกบูธ</td>
                                <td class="pl-2 text-end font-weight-bold">{{ team.team_sale_total_sum }}</td>
                                <td class="pl-2 text-end font-weight-bold">{{ team.team_sale_count_sum }}</td>
                            </tr>

                            <template v-for="(stall) in team_sale_list" :key="stall.stall_number">
                                <tr v-for="(sale, sale_index) in stall.sales" :key="stall.stall_number+sale.sale_date+sale.sale_date+sale.sale_total+sale.sale_count">
                                    <td><template v-if="sale_index==0"><b>{{ stall.stall_number }}</b></template></td>
                                    <td class="pl-2 text-end"><b>{{ sale.sale_date }}</b></td>
                                    <td class="pl-2 text-end">{{ sale.sale_total }}</td>
                                    <td class="pl-2 text-end">{{ sale.sale_count }}</td>
                                </tr>
                            </template>
                              
                        </table>



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

                        <table v-if="stall_sale_list.length">
                            <tr><td>วันที่</td><td class="pl-2 text-center">ยอดขาย</td><td class="pl-2 text-center">จำนวน</td></tr>
                            <tr v-for="sale in stall_sale_list" :key="sale.sale_date+sale.sale_total+sale.sale_count">
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
        select_team: '',
        team: null,
        team_list: [],

        stall_sale_list: [],
        team_sale_list: [],


        headers: [
            { text: 'วันที่', value: 'sale_date', align: 'center', sortable: false, fixed: true},
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
        },
        select_team(){
            this.getTeamSales()
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

            axios
            .get('/api/teams').then(response => {
                if (response.data.success == true) {
                    this.team_list = response.data.team_list
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

        getTeamSales(){
            this.loading = true

            console.log("getting team sales");

            axios
            .get('/api/team/sales', {params:{team:this.select_team}}).then(response => {
                if (response.data.success == true) {
                    this.team_sale_list = []
                    this.team_sale_list = response.data.team_sale_list
                    this.team = response.data.team
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
<style lang="css" scoped>
.table {
  max-height: calc(100vh - 300px);
  overflow-y: auto;
}
</style>