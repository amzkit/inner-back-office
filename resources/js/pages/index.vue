<template>
    <div class="container">
        <v-row justify="center" class="pl-2 pr-2 mb-1">
            <v-col class="my-1 py-0 ">
                <v-card :loading="loading">
                    <v-card-title>
                    </v-card-title>

                    <v-card-text>
                        <a href="/statistic" class="">ข้อมูลย้อนหลัง</a>
                        <br>
                        <a href="/import" class="">Import ข้อมูลการขาย</a>
                        <br>
                        <a href="/people" class="">ข้อมูลบุคคล</a>
                        <br>

                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>

import LineChart from '../components/chart.vue'
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