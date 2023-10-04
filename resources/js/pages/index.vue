<template>
    <div class="container">
        <v-row justify="center" class="pl-2 pr-2 mb-1">
            <v-col class="my-1 py-0 ">
                <v-card :loading="loading">
                    <v-card-title>
                    </v-card-title>

                    <v-card-text>
                        <a href="/statistic" class="">ข้อมูลย้อนหลัง</a> <template v-if="last_update!=null">(Updated on {{ last_update }})</template>
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

export default {
    components: {  },
    data: () => ({
        loading: true,
        last_update: null


    }),

    computed: {

    },

    watch: {
        
    },

    created () {
        this.loading = "indigo"
        this.initialize()
    },

    methods: {
        initialize(){
            this.loading = "indigo"

            return axios
            .get('/api/home').then(response => {
                if (response.data.success == true) {
                    this.last_update = response.data.last_update
                }
                this.loading = false

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