<template>
    <v-app>
        <v-main>
            <v-card :loading="loading">
                <v-card-title>Upload Sales</v-card-title>
                <v-card-text>
                    <v-file-input
                        v-model="file"
                        label="เลือกไฟล์"
                        truncate-length="40"
                        small-chips
                        @change="onFileSelected"
                        ref="file"
                        id="file"
                        dense
                    ></v-file-input>
                    <template v-if="success">
                        <div class="text-black text-center">Success <v-icon icon="mdi-check-circle-outline" color="success"></v-icon></div>
                    </template>
                </v-card-text>            
            </v-card>



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

            file: null,
            success: false,
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
    
        },
        onFileSelected(event){
            if(event == null)   return
            this.loading = true
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
                        this.success = true    
                    }else{
                        this.success = false
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
