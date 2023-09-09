<template>
    <div class="container">
        <v-card class="align-center pb-0" density="compact">
            <v-card-title class="">
                <div class="d-flex justify-between">
                    <div class="pr-2">ค้นหา</div>
                    <v-btn color="green" density="" target="_people" :href="'/people/create'">
                        <v-icon icon="mdi-plus"></v-icon> เพิ่มคน
                    </v-btn>
                </div>
            </v-card-title>
            <v-card-text class="pb-1">
                <div class="d-flex align-justify-center mb-2">
                    <v-text-field v-model="search_text" variant="outlined" placeholder="ค้นหา" density="compact" hide-details>
                    </v-text-field>
                    <v-btn class="mt-1 ml-2" color="indigo-darken-3">
                        <v-icon color="white">
                            mdi-magnify
                        </v-icon>
                        ค้นหา
                    </v-btn>
                </div>
                <div v-if="!advanced_search" class="text-center" @click="advanced_search=true"><v-btn variant="text" density="densed" color="indigo-darken-3">เพิ่มเติม</v-btn></div>
                <template v-if="advanced_search">
                        <v-select variant="outlined" density="compact"
                            class="mb-2"
                            v-model="search_role_select"
                            label="ประเภท"
                            :items="search_role_list"
                            hide-details
                        ></v-select>

                        <v-select variant="outlined" density="compact"
                            class="mb-2"
                            v-model="sort_type_select"
                            label="เรียงตาม"
                            :items="sort_type_list"
                            hide-details
                        ></v-select>

                    </template>
            </v-card-text>
        </v-card>
        <v-row><v-col></v-col></v-row>

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
                                    
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="person in people"
                                :key="person.id"
                                @click="redirect(person)"
                                style="cursor:pointer;"
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
        
        search_text: '',
        search_role_select: null,
        search_role_list: [],
        sort_type_select: null,
        sort_type_list: ['วีซ่าหมดอายุก่อน', 'อายุมากสุดก่อน'],
        advanced_search: false,

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
            { title: 'Passport ID', key: 'date_of', align: 'center' },

        ],
        serverItems: [],
        loading: true,
        totalItems: 0,


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
        redirect(person){
            window.location.href = "/people/" + person.id
        },
        initialize(){
            this.loading = true

            axios
            .get('/api/people').then(response => {
                if (response.data.success == true) {
                    this.people = response.data.people
                    this.search_role_list = response.data.people_role
                }
            })
            .catch(error => {
                console.log("getting data error");
            });
            this.loading = false
        },
        person_select(person){
            console.log(person)
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