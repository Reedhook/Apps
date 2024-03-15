<script>
import {isDisabled} from "bootstrap/js/src/util/index.js";

export default {
    name: "Registration",
    data() {
        return {
            email: '',
            is_admin: false,
            error_email: null
        }
    },
     methods:{
        isDisabled,
        store(){
            axios.post('/api/auth/registration', {email: this.email, is_admin: this.is_admin})
                .then(res=>{
                    if(res){
                        this.$router.push({name: 'user.login'})
                    }
                })
                .catch(error=>{
                    if(error.response.data.errors){
                        this.error_email = error.response.data.errors.email[0]
                    }
                    console.log(error.response.data.errors);

                })
        }
     },
    computed: {
        isDisabled() {
            return this.email
        }
    }
}
</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="row mb-3">
            <h3>Registration</h3>
        </div>
            <div class="w-25 border border rounded p-4">
                <input v-model="email" type="email" class="form-control mt-3 mb-3" placeholder="email">
                <div v-if="error_email" class="text-danger">{{ this.error_email }}</div>
                <div class="form-check form-switch">
                    <input v-model="is_admin" class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
                    <label class="form-check-label" for="flexSwitchCheckChecked">Admin</label>
                </div>
                <input :disabled="!isDisabled" @click.prevent="store" type="submit" value="add" class="btn btn-primary mb-3">
            </div>
        </div>
</template>

<style scoped>

</style>
