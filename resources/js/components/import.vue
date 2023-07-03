<template>
    <v-app>
        <v-main>

            <v-overlay :value="loading"><v-progress-circular indeterminate size="64"></v-progress-circular></v-overlay>

            <v-file-input
                v-model="file"
                label="เลือกไฟล์"
                truncate-length="40"
                small-chips
                @change="onFileSelected"
                ref="file"
                id="file"
                :hint="hint"
                dense
            ></v-file-input>

            <template v-if="false">
            <v-card :loading="loading">
                <v-card-title>นำเข้าคำสั่งซื้อ</v-card-title>
                <v-card-subtitle>เลือกข้อมูลที่ต้องการนำเข้า</v-card-subtitle>

                <v-card-text>
                    <form>
                        <v-select
                            v-model="type"
                            :items="type_options"
                            label="ประเภท"
                            dense
                        >
                        </v-select>
                        <v-file-input
                            v-if="type=='linemyshop_json'"
                            v-model="file"
                            label="เลือกไฟล์"
                            truncate-length="40"
                            small-chips
                            @change="onFileSelected"
                            ref="file"
                            id="file"
                            :hint="hint"
                            dense
                        ></v-file-input>
                        <template v-if="type=='linemyshop_data'">
                        <div>
                            1. เข้าไปใน LINE MyShop ผ่านเวปบราวเซอร์<br>
                            2. ยืนยันการชำระเงินของรายการสั่งซื้อทั้งหมดให้เรียบร้อย
                            3. คลิ๊กขวาในเวปแล้วเลือกเมนู Inspect (ตรวจสอบ) > เลือกแทบ Network (เครือข่าย)<br>
                            4. ในเวป LINE MyShop เปิดเมนู > การจัดส่ง<br>
                            5. เลือกรายการสั่งซื้อที่ต้องการนำเข้าทั้งหมด<br>
                            6. กลับมาที่หน้า Inspect ตรงช่อง Name (รายการ) ให้เลือก "print?order_no=202..."<br>
                            7. ตรง Preview (แสดงผล) ให้คลิ๊กขวาตรง data แล้วเลือก "copy value"<br>
                            8. นำข้อมูลที่คัดลอกมาแล้วมาวางไว้ในช่องข้อความด้านล่างนี้
                        </div>
                        <v-textarea
                            label="กรุณาคัดลอกคำสั่งซื้อจาก Linemyshop แล้ววางลงที่นี่"
                            v-model="data"
                            :hint="hint"
                        ></v-textarea>
                        <v-btn @click="verifyLineMyShopData" :disabled="!data.length">ตรวจสอบ</v-btn>
                        </template>
                        <v-divider />
                        <v-select
                            v-if="orders.length"
                            v-model="select_other_payment_channel"
                            :items="other_payment_channels"
                            label="บัญชีรับเงิน"
                            dense
                        >
                        </v-select>
                        <v-select
                            v-if="orders.length"
                            v-model="select_cod_payment_channel"
                            :items="cod_payment_channels"
                            label="บัญชีรับปลายทาง"
                            dense
                        >
                        </v-select>
                    </form>
                </v-card-text>
                <v-card-actions></v-card-actions>
            </v-card>
            <v-card class="mt-2" :loading="loading">
                <v-card-title>นำเข้าคำสั่งซื้อ</v-card-title>
                <v-card-subtitle>นำเข้าคำสั่งซื้อโดยการใช้ไฟล์</v-card-subtitle>
                <v-card-text>
                    <v-spacer />
                    <v-data-table
                        v-if="orders.length"
                        :headers="headers"
                        :items="orders"
                        class="elevation-1"
                    >
                        <template v-slot:items="props">
                        <td>{{ props.item.shipping_address.name }}</td>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="submit" :disabled="!orders.length">ยืนยัน</v-btn>
                    <v-btn @click="clear">ยกเลิก</v-btn>
                </v-card-actions>
            </v-card>
            </template>
        </v-main>
    </v-app>
</template>

<script>

export default {
    name: 'import',
    components: { },
    props: [],
    data() {
        return {
            loading: false,

            showStatusDialog: false,
            statusColor: '',
            statusText: '',
            statusIcon: '',

            el: 1,

            import_data_type: null,
            import_data_type_options: [
                {value:'order', text: 'คำสั่งซื้อ'},
                {value:'shipment', text: 'เลขพัสดุ'},
            ],

            import_order_format: null,
            import_order_format_options: [
                {value:'text_flashtoy_format', text: 'รูปแบบ Flashtoy'},
                {value:'linemyshop_xlsx', text:'Line My Shop (.xlsx)'},
                {value:'linemyshop_data', text:'Line My Shop (Text)'}
            ],

            import_shipment_format: null,
            import_shipment_format_options: [
                {value:'ems_xls', text: 'EMS คำเที่ยง (.xls)'},
            ],

            // for linemyshop_json (file)
            file: null,
            // for linemyshop_data
            data: "",

            orders: [],
            unknown_orders: [],
            other_payment_channels: [],
            cod_payment_channels: [],

            select_other_payment_channel:{},
            select_cod_payment_channel:{},

            headers:[
                {   text: 'ลำดับ', align: 'left', sortable: false, value: 'index' },
                {   text: 'เวลา', align: 'left', sortable: false, value: 'datetime' },
                {   text: 'ประเภท', align: 'left', sortable: false, value: 'cached_shop_payment_channel_type' },
                {   text: 'ชื่อ', align: 'left', sortable: false, value: 'name' },
                //{   text: 'ชื่อ', align: 'left', sortable: false, value: 'shipping_address.recipient_name' },
                //{   text: 'เบอร์โทร', align: 'left', sortable: false, value: 'shipping_address.phone_number' },
                {   text: 'รายการสินค้า', align: 'left', sortable: false, value: 'items' },
            ],




            default:{
                import_data_type: "order",
                import_order_format: "linemyshop_data",
                import_shipment_format: "ems_xls",

                file: null,
                data: "",
                orders: [],
            },

            new_order_count: 0,
            existed_order_count: 0,

        }
    },
    computed: {

    },

    watch: {


    },
    mounted: function() {

        //this.initialize()
    },
    methods: {
        async initialize () {
            return axios.get("/api/import/order/create")
                .then(response => {
                    if (response.data.success == true) {
                        this.other_payment_channels = response.data.other_payment_channels
                        this.cod_payment_channels = response.data.cod_payment_channels

                        console.log(response.data.default_payment_channel)
                        if(response.data.default_payment_channel != null){
                            this.select_other_payment_channel = response.data.default_payment_channel
                        }else{
                            this.select_other_payment_channel = this.other_payment_channels[0]
                        }

                        if(response.data.default_cod_payment_channel != null){
                            this.select_cod_payment_channel = response.data.default_cod_payment_channel
                        }else{
                            this.select_cod_payment_channel = this.cod_payment_channels[0]
                        }

                    }
                    this.loading = false

                })
                .catch(error => {
                    console.log("updating data error", error.response.data.errors);
                    let errors = error.response.data.errors
                    let error_message = errors[Object.keys(errors)[0]]


                    this.loading = false
                });


        },


        onFileSelected(event){
            if(event == null)   return
            
            var uploadFile = document.querySelector('#file');
            console.log("File Selected", uploadFile.files[0].name)
            var formData = new FormData();
            formData.append("file", uploadFile.files[0]);
            formData.append("type", "xlsx");


            return axios.post("/api/import/sales", formData ,{
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },

                }).then(response => {
                    if (response.data.success == true) {
                        if(this.import_data_type == 'order'){
                            this.orders = response.data.orders
                            this.unknown_orders = response.data.unknown_orders
                        }else if(this.import_data_type == 'shipment'){
                            this.orders = response.data.shipments
                            this.unknown_orders = response.data.unknown_shipments
                        }
                        
                    }else{

                    }
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                    console.log('Upload Error', error)
                });
        },

    },
}
</script>

<style></style>
