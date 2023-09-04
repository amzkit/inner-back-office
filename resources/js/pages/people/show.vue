<template>
    <div class="container">
        <v-card class="align-center" density="compact">
            <v-card-title class="">
                <div class="d-flex justify-between">
                    <div class="pr-2">ข้อมูล</div>
                </div>
            </v-card-title>
            <v-card-text class="pt-3 pb-1">
                <div class="d-flex flex-column">
                    <div class="my-1"><v-select v-model="person.type" :items="type_list" variant="outlined" label="ประเภท" density="compact" hide-details></v-select></div>

                    <div class="my-1"><v-text-field v-model="person.firstname" label="ชื่อ" variant="outlined" placeholder="ชื่อ" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.lastname" label="นามสกุล" variant="outlined" placeholder="นามสกุล" density="compact" hide-details /></div>
                    <div class="my-1"><v-select v-model="person.gender" :items="gender_list" variant="outlined" label="เพศ" density="compact" hide-details></v-select></div>
                    <div class="my-1"><v-text-field v-model="person.mobile_number" label="เบอร์โทรศัพท์" variant="outlined" placeholder="เบอร์โทรศัพท์" density="compact" hide-details /></div>

                    <div class="my-1"><v-text-field v-model="person.passport_id" label="หมายเลขหนังสือเดินทาง" variant="outlined" placeholder="หมายเลขหนังสือเดินทาง" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.passport_issue_date" label="วันที่ออกหนังสือเดินทาง" variant="outlined" placeholder="วันที่ออกหนังสือเดินทาง" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.passport_expiry_date" label="วันที่หนังสือเดินทางหมดอายุ" variant="outlined" placeholder="วันที่หนังสือเดินทางหมดอายุ" density="compact" hide-details /></div>

                    <div class="my-1"><v-text-field v-model="person.arrival_date" label="วันที่เดินทางเข้าประเทศจีน" variant="outlined" placeholder="วันที่เดินทางเข้าประเทศจีน" density="compact" hide-details /></div>
                    <v-date-picker show-adjacent-months></v-date-picker>

                </div>
            </v-card-text>
            <v-card-actions class="d-flex justify-center">
                <div class=""><v-btn variant="flat" color="grey">ยกเลิก</v-btn><v-btn variant="flat" color="primary">บันทึก</v-btn></div>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>

export default {
    components: {  },
    props: ['id'],
    data: () => ({
        loading: true,
        
        showDatePickerDialog: false,
        date_select: '',

        person: {
            firstname: '',
            lastname: '',
            mobile_number: '',
            date_of_birth: '',

            passport_id: '',
            passport_expiry_date: '',
            passport_issue_date: '',

            type: '',

            arrival_date: '',
        },

        type_list: [
            {title: 'Vendor-Owner', value: 'VENDER-OWNER'},
            {title: 'Vendor-Staff', value: 'VENDER-STAFF'},
            {title: 'Inner', value: 'INNER'},
            {title: 'Inner-Staff', value: 'INNER-STAFF'},
            {title: 'Other', value: 'Other'},


        ],
        gender_list: [
            {title:'Male', value:'M'},
            {title:'Female', value:'F'},
        ],

    }),

    computed: {
    },

    watch: {

    },

    created () {
        this.loading = false
        this.initialize()
    },

    methods: {
        initialize(){
            this.loading = true

            axios.get('/api/people/'+this.id).then(response => {
                if (response.data.success == true) {
                    this.person = response.data.people
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