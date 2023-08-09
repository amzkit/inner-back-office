<template>
    <div class="container">
        <v-card class="align-center pb-0" density="compact">
            <v-card-title class="">ค้นหา</v-card-title>
            <v-card-text class="pb-1">
                <div class="d-flex align-justify-center mb-2">
                    <v-text-field v-model="search_text" variant="outlined" placeholder="ค้นหา" density="compact" hide-details>
                    </v-text-field>
                    <v-btn class="mt-1 ml-2" color="primary">
                        <v-icon color="white">
                            mdi-magnify
                        </v-icon>
                        ค้นหา
                    </v-btn>
                </div>
                <div v-if="!advanced_search" class="text-center" @click="advanced_search=true">เพิ่มเติม</div>
                <template v-if="advanced_search">
                        <v-select variant="outlined" density="compact"
                            class="mb-2"
                            v-model="search_type_select"
                            label="ประเภท"
                            :items="search_type_list"
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
                                    Passport ID
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="person in people"
                                :key="person.id"
                                @click="person_select(person)"
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
        search_type_select: null,
        search_type_list: ['ผู้ประกอบการ', 'ลูกจ้าง', 'พนักงาน'],
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
            { title: 'Passport ID', key: 'passport_id', align: 'center' },

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