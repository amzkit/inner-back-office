<template>
    <div class="container">
        <v-row class="align-center">
            <v-col>
                <div class="d-flex align-justify-center">
                        <v-text-field v-model="search_text" variant="outlined" placeholder="ค้นหา" density="compact">
                        </v-text-field>
                        <v-btn class="mt-1 ml-2" color="primary">
                            <v-icon color="white">
                                mdi-magnify
                            </v-icon>
                            ค้นหา
                        </v-btn>
                </div>
            </v-col>
        </v-row>
        <v-row justify="center" class="mb-1">
            <v-col class="my-1 py-0 ">
                <v-card :loading="loading">

                    <v-card-title>
                    <span class="">ข้อมูล</span>
                    </v-card-title>

                    <v-card-text>
                        <v-table
                            fixed-header
                            hover
                            density="compact">
                            <thead>
                            <tr>
                                <th class="text-left">
                                    ID
                                </th>
                                <th class="text-left">
                                    Firstname
                                </th>
                                <th class="text-left">
                                    Lastname
                                </th>
                                <th class="text-left">
                                    Passport ID
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="person in people"
                                :key="person.id"
                            >
                                <td>{{ person.id }}</td>
                                <td>{{ person.firstname }}</td>
                                <td>{{ person.lastname }}</td>
                                <td>{{ person.passport_id }}</td>

                            </tr>
                            </tbody>
                        </v-table>


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
        
        people: [],

        itemsPerPage: 5,
        headers: [
            {
            title: 'ID',
            align: 'start',
            sortable: false,
            key: 'id',
            },
            { title: 'Firstname', key: 'firstname', align: 'center' },
            { title: 'Lastname', key: 'lastname', align: 'center' },
            { title: 'Passport ID', key: 'passport_id', align: 'center' },

        ],
        serverItems: [],
        loading: true,
        totalItems: 0,

        search_text: '',

    }),

    computed: {
    },

    watch: {
        select_stall_number(){
        },
        select_team(){
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
            .get('/api/people').then(response => {
                if (response.data.success == true) {
                    this.people = response.data.people
                }
            })
            .catch(error => {
                console.log("getting data error");
            });
            this.loading = false
        },
        loadItems(){

        }
    },
};
</script>
<style lang="css" scoped>
.table {
  max-height: calc(100vh - 300px);
  overflow-y: auto;
}
</style>