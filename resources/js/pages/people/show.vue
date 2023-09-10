<template>
    <div class="container">

        <v-dialog
            v-model="showDatePickerDialog"
            width="300"
            persistent
        >
            <v-date-picker
                header="เลือกวันที่"
                v-model="date_selected"
                no-title
                @click:cancel="showDatePickerDialog=false"
                @click:save="showDatePickerDialog=false"
            >
            </v-date-picker>
        </v-dialog>
        <v-card class="align-center" density="compact">
            <v-card-title class="">
                <div class="d-flex justify-between">
                    <div class="pr-2 pt-2 text-indigo-darken-3">ข้อมูลบุคคล</div>
                </div>
            </v-card-title>
            <v-card-text class="pt-3 pb-1">
                <div class="d-flex flex-column">
                    <div class="pr-2 pt-0 pb-2 font-weight-bold text-indigo-darken-3">ข้อมูลส่วนตัว</div>
                    <div class="my-1"><v-select v-model="person.role" :items="role_list" variant="outlined" label="ประเภท (Role)" density="compact" hide-details></v-select></div>

                    <div class="my-1"><v-text-field v-model="person.firstname" label="ชื่อ ภาษาอังกฤษ (First Name) *" variant="outlined" placeholder="ชื่อ" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.lastname" label="นามสกุล ภาษาอังกฤษ (Last Name) *" variant="outlined" placeholder="นามสกุล" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.firstname_th" label="ชื่อ ภาษาไทย" variant="outlined" placeholder="ชื่อ" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.lastname_th" label="นามสกุล ภาษาไทย" variant="outlined" placeholder="นามสกุล" density="compact" hide-details /></div>
                    <div class="my-1"><v-select v-model="person.gender" :items="gender_list" variant="outlined" label="เพศ (Gender)" density="compact" hide-details></v-select></div>
                    <div class="my-1"><v-text-field v-model="person.mobile_number" type="number" label="เบอร์โทรศัพท์ (Mobile Number)" variant="outlined" placeholder="เบอร์โทรศัพท์" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.date_of_birth" :max="today" type="date" label="วันเกิด (Date of Birth)" variant="outlined" placeholder="วันเกิด" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.national_id_number" label="หมายเลขบัตรประชาชน (National ID Number)" variant="outlined" placeholder="หมายเลขบัตรประชาชน" density="compact" hide-details /></div>


                    <div class="pr-2 pt-4 pb-2 font-weight-bold text-indigo-darken-3">ข้อมูลหนังสือเดินทาง</div>
                    <div class="my-1"><v-text-field v-model="person.passport_id" label="หมายเลขหนังสือเดินทาง (Passport ID)" variant="outlined" placeholder="หมายเลขหนังสือเดินทาง" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.passport_issue_date" :max="today" type="date" label="วันที่ออกหนังสือเดินทาง (Issued Date)" variant="outlined" placeholder="วันที่ออกหนังสือเดินทาง" density="compact" hide-details /></div>
                    <div class="my-1"><v-text-field v-model="person.passport_expiry_date" :min="today" type="date" label="วันที่หนังสือเดินทางหมดอายุ (Expiry Date)" variant="outlined" placeholder="วันที่หนังสือเดินทางหมดอายุ" density="compact" hide-details /></div>
                </div>
            </v-card-text>
            <v-card-actions class="d-flex justify-center">
                <div class=""><v-btn variant="flat" color="grey">ยกเลิก</v-btn><v-btn variant="flat" color="indigo-darken-3" @click="store">บันทึก</v-btn></div>
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
        
        today: null,
        showDatePickerDialog: false,
        date_selected: null,
        person: {},

        person_default: {
            firstname: '',
            lastname: '',
            firstname_th: '',
            lastname_th: '',
            mobile_number: '',
            date_of_birth: '',

            passport_id: '',
            passport_expiry_date: '',
            passport_issue_date: '',

            role: 'VENDER-OWNER',
            gender: 'U',
            national_id_number: '',
        },

        role_list: [

        ],
        gender_list: [

        ],

    }),

    computed: {

    },

    watch: {

    },

    created () {
        this.loading = false
        this.initialize()
        this.today = moment().format('YYYY-MM-DD')
        console.log(this.today)
    },

    methods: {
        initialize(){
            this.loading = true

            axios.get('/api/people/'+this.id).then(response => {
                if (response.data.success == true) {

                    if(response.data.people != null){
                        this.person = response.data.people
                    }else{
                        this.person = this.person_default
                    }
                    this.role_list = response.data.people_role
                    this.gender_list = response.data.people_gender
                }
            })
            .catch(error => {
                console.log("getting data error");
            });
            this.loading = false
        },
        person_select(person){
            console.log(person)
        },
        store(){
            this.loading = true
            return axios.post("/api/people/store", {
                    person: this.person
                }).then(response => {
                    if (response.data.success == true) {
                        this.success = true    
                        window.location.href="/people"
                    }else{
                        this.success = false
                    }
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                    console.log('Store Error', error)
                });
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