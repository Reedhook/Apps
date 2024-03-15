<script>
export default {
    name: "ResetPassword",
    data() {
        return {
            email: '',
            password: '',
            password_confirmation: '',
            error_email: '',
            error_password: '',
            error_password_confirmation: '',
            showPassword: false,
            token: '',
        }
    },
    methods: {
        reset() {
            axios.post('/api/auth/reset/', {
                token: this.token,
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation
            })
                .then(res => {
                    console.log(res)
                    this.$router.push({name: 'user.login'})
                })
                .catch(error => {
                    console.log(error);
                    if (error.response.data.errors.email) {
                        this.error_email = error.response.data.errors.email[0]
                    }
                    if (error.response.data.errors.password) {
                        this.error_password = error.response.data.errors.password[0]
                    }
                    if (error.response.data.errors.password_confirmation) {
                        this.error_password_confirmation = error.response.data.errors.password_confirmation[0]
                    }
                })
        },
        togglePasswordVisibility() {
            this.showPassword = !this.showPassword;
        }
    },
    created() {
        this.token = this.$route.params.token;
    },
    computed: {
        isDisabled() {
            return this.email && this.password && this.password_confirmation;
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
            <h3>Reset</h3>
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
            <div class="password-input-container">
                <input v-model="password_confirmation" :type="passwordFieldType" class="form-control mb-3"
                       placeholder="Confirm password">
                <button @click="togglePasswordVisibility" class="password-toggle-button">
                    <i :class="showPassword ? 'gg-toggle-on' : 'gg-toggle-off'"></i>
                </button>
            </div>
            <div v-if="error_password_confirmation" class="text-danger">{{ this.error_password_confirmation }}</div>
            <input :disabled="!isDisabled" @click.prevent="reset" type="submit" value="Reset" class="btn btn-primary">
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
