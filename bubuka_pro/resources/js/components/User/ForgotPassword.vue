<script>
export default {
    name: "ForgotPassword",
    data() {
        return {
            email:null,
        }
    },
    methods:{
        to_send(){
            axios.post('/api/auth/forgot/', {email: this.email})
        }
    },
    computed:{
      isDisabled(){
          return this.email;
      }
    }
}
</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="row mb-3">
            <h3>Forgot Password</h3>
        </div>
        <div class="w-25 border border rounded p-4" style="position: relative;">
            <input v-model="email" type="email" class="form-control mt-3 mb-3" placeholder="email">
            <input :disabled="!isDisabled" @click.prevent="to_send" type="submit" value="Send" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
