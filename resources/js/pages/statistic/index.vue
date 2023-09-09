<template>
    <div class="container">
        <v-row justify="center" class="pl-2 pr-2 mb-1">
            <v-col class="my-1 py-0 ">
                <v-card :loading="loading">
                    <v-card-title>
                        <span class="">ข้อมูลย้อนหลัง</span>
                    </v-card-title>

                    <v-card-text>
                        <v-switch v-model="select_show_never_open_stall" label="แสดงร้านที่ไม่เคยเปิด" density="densed" color="primary"></v-switch>
                        <v-select
                            v-model="select_date_range_month"
                            :items="date_range_month_list"
                            item-text="name"
                            label="เดือน"
                            dense
                            required
                        />
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
                                <tr>
                                    <template v-for="(stall_sale_total_sum, index) in team_sale_list.team_sale_total_sum" :key="stall_sale_total_sum+index">
                                        <th class="pl-2 text-center">{{ stall_sale_total_sum }}</th>
                                    </template>
                                </tr>
                                <tr>
                                    <th class="pl-2 text-end"></th>
                                    <template v-for="(stall_number, index) in team_sale_list.header" :key="stall_number+index">
                                        <th class="pl-2 text-end font-weight-bold">{{ stall_number }}</th>
                                    </template>
                                </tr>
                                </thead>


                                <template v-for="(sales, date) in team_sale_list.sales_by_date" :key="date">
                                    <tr>
                                        <td class="text-end font-weight-bold"><b>{{ date }}</b></td>
                                        <template v-for="(sale, index) in sales" :key="date+sale+index">
                                            <template v-if="date == 'รวม' || index == sales.length -1 ">
                                                <td class="pl-2 text-end font-weight-bold"><span :class="sale == '0.00'?'text-danger':''">{{ sale }}</span></td>
                                            </template>
                                            <template v-else>
                                                <td class="pl-2 text-end"><span :class="sale == '0.00'?'text-danger':''">{{ sale }}</span></td>
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
                                
                                <template v-if="true">
                                    <thead>
                                        <tr>

                                        </tr>
                                    </thead>

                                    <tr style="border-top: 1px solid #ddd" v-if="typeof insight.stall_sales_sum != 'undefined'">
                                        <td class="text-end font-weight-bold">รวม</td>
                                        <td class="pl-2 text-end font-weight-bold" v-for="(sales_sum, index) in insight.stall_sales_sum" :key="'index_'+index">
                                            <span :class="no_sale_day_count == day_count?'text-danger': no_sale_day_count == 0?'text-success':''">{{ sales_sum }}</span>
                                        </td>
                                    </tr>
                                    <tr v-if="typeof insight.stall_sales_average != 'undefined'">
                                        <td class="text-end font-weight-bold">เฉลี่ย</td>
                                        <td class="pl-2 text-end font-weight-bold" v-for="(sales_avg, index) in insight.stall_sales_average" :key="'index_'+index">
                                            <span :class="no_sale_day_count == day_count?'text-danger': no_sale_day_count == 0?'text-success':''">{{ sales_avg }}</span>
                                        </td>
                                    </tr>
                                    <tr v-if="typeof insight.stall_sales_max != 'undefined'">
                                        <td class="text-end font-weight-bold">สูงสุด</td>
                                        <td class="pl-2 text-end font-weight-bold" v-for="(sales_max, index) in insight.stall_sales_max" :key="'index_'+index">
                                            {{ sales_max }}
                                        </td>
                                    </tr>
                                    <tr v-if="typeof insight.stall_sales_min != 'undefined'">
                                        <td class="text-end font-weight-bold">ต่ำสุด</td>
                                        <td class="pl-2 text-end font-weight-bold" v-for="(sales_min, index) in insight.stall_sales_min" :key="'index_'+index">
                                            {{ sales_min }}
                                        </td>
                                    </tr>
                                    <tr style="border-top: 1px solid #ddd">
                                        <td class="text-end font-weight-bold">วันปิด</td>
                                        <td class="pl-2 text-end font-weight-bold" v-for="(no_sale_day_count, index) in insight.stall_no_sale_day_count" :key="'index_'+index">
                                            <span :class="no_sale_day_count == day_count?'text-danger': no_sale_day_count == 0?'text-success':''">{{ no_sale_day_count }}</span>
                                        </td>
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

export default {
    components: {  },
    data: () => ({
        loading: true,
        select_stall_number: '',
        stall_list: [],
        select_team: '',
        team: null,
        team_list: [],

        stall_sale_list: [],
        team_sale_list: [],
        insight: [],

        headers: [
            { text: 'วันที่', value: 'sale_date', align: 'center', sortable: false, fixed: true},
            { text: 'ยอด', value: 'sale_total', align: 'start' , sortable: false},
            { text: 'จำนวน', value: 'sale_count', align: 'center' , sortable: false},
        ],

        // Date Time Range
        select_date_range_month: 9,
        date_range_month_list: [
            {value: -1, title:  "ทั้งหมด"},
            {value: 6,  title:  "มิถุนายน"},
            {value: 7,  title:  "กรกฎาคม"},
            {value: 8,  title:  "สิงหาคม"},
            {value: 9,  title:  "กันยายน"},
        ],

        select_show_never_open_stall: false,

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
        day_count(){
            let count = 0
            if(typeof this.insight.day_count !== 'undefined'){
                count = this.insight.day_count
            }
            return count
        }
    },

    watch: {
        select_stall_number(){
            this.getStallSales()
        },
        select_team(){
            this.getTeamSales()
        },
        select_date_range_month(){
            this.getTeamSales()
        },
        select_show_never_open_stall(){
            this.getTeamSales()
        }
    },

    created () {
        this.loading = false
        this.initialize()
    },

    methods: {
        initialize(){
            this.loading = "primary"

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
            this.loading = "primary"

            console.log("getting team sales");

            return axios.get('/api/team/sales', {
                params:{
                    team:   this.select_team,
                    date_range_month: this.select_date_range_month,
                    show_never_open_stall: this.select_show_never_open_stall,
                }
            }).then(response => {
                if (response.data.success == true) {
                    this.team_sale_list = []
                    this.team_sale_list = response.data.team_sale_list
                    this.team = response.data.team
                    this.insight = response.data.insight
                    // console.log(this.stall_sale_list)
                    this.loading = false

                }
            })
            .catch(error => {
                console.log("getting data error");
                this.loading = false

            });
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