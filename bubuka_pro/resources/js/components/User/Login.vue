<script>
import {isDisabled} from "bootstrap/js/src/util/index.js";

export default {
    name: "Login",
    data() {
        return {
            email: '',
            password: '',
            error_email: '',
            error_password: '',
            showPassword: false
        }
    },
    methods: {
        login() {
            axios.post('/api/auth/login', {email: this.email, password: this.password})
                .then(res => {
                    localStorage.access_token = res.data.access_token;
                    localStorage.refreshToken = res.data.refreshToken;
                    this.$router.push({name: 'home'})
                })
                .catch(error=>{
                    console.log(error);
                    if(error.response.data.error === 'Unauthorized'){
                        this.error_email = 'Неправильный логин или пароль'
                    }
                    if(error.response.data.errors.email){
                        this.error_email = error.response.data.errors.email
                    }
                    if(error.response.data.errors.password){
                        this.error_password = error.response.data.errors.password
                    }
                })
        },
        togglePasswordVisibility() {
            this.showPassword = !this.showPassword;
        }
    },
    computed: {
        isDisabled() {
            return this.email && this.password;
        },
        passwordFieldType() {
            return this.showPassword ? 'text' : 'password';
        }
    }
}
</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="row mb-3">
            <h3>Log in</h3>
        </div>
        <div class="w-25 border border rounded p-4" style="position: relative;">
            <input v-model="email" type="email" class="form-control mt-3 mb-3" placeholder="email">
            <div v-if="error_email" class="text-danger">{{ this.error_email }}</div>
            <div class="password-input-container">
                <input v-model="password" :type="passwordFieldType" class="form-control mb-3" placeholder="password">
                <button @click="togglePasswordVisibility" class="password-toggle-button">
                    <i :class="showPassword ? 'gg-toggle-on' : 'gg-toggle-off'"></i>
                </button>
            </div>
            <div v-if="error_password" class="text-danger">{{ this.error_password }}</div>
            <input :disabled="!isDisabled" @click.prevent="login" type="submit" value="Log in" class="btn btn-primary">
            <router-link :to="{name:'user.forgot'}">Забыли пароль?</router-link>
        </div>
    </div>
</template>

<style scoped>
.password-input-container {
    position: relative;
}

.password-toggle-button {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    cursor: pointer;
}

</style>
