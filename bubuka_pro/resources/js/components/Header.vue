<script>
import api from "../api.js";

export default {
    name: "Header",
    data() {
        return {
            accessToken: null
        }
    },
    mounted() {
        this.getAccessToken()
    },
    methods: {
        getAccessToken() {
            this.accessToken = localStorage.getItem('access_token')
        },
        logout() {
            api.post('/api/auth/logout')
                .then(res => {
                    console.log(res)
                    this.$router.push({name: 'user.login'})
                })
                .catch(error => {
                    console.log(error)
                    localStorage.removeItem('access_token')
                    localStorage.removeItem('refreshToken')
                    this.$router.push({name: 'user.login'})
                })
        }
    },
    updated() {
        this.getAccessToken()
    }
}
</script>

<template>
    <div class="container-fluid">
        <div v-if="accessToken" class="row">
            <div class="col-lg-1">
                <router-link :to="{name: 'home'}" class="header-words">Home</router-link>
            </div>
            <div class="col-lg-1">
                <a href="#" @click.prevent="logout" class="header-words">Log out <i class="gg-log-out"></i></a>
            </div>
        </div>

        <div v-if="!accessToken" class="row">
            <div v-if="$route.name !== 'user.registration'" class="col-lg-1">
                <router-link :to="{name: 'user.registration'}" class="header-words"> Registration </router-link>
            </div>
            <div v-if="$route.name !== 'user.login'" class="col-lg-1">
                <router-link :to="{name: 'user.login'}" class="header-words">
                    <span>Sign in</span>
                    <i class="gg-log-in"></i>
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>

.container-fluid {
    margin-top: 10px;
    background: black;
    height: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.header-words {
    margin-left: 10px;
    color: white;
    display: flex;
    align-items: center;
}

.header-words i {
    margin-left: 20px; /* Установите желаемый отступ между текстом и значком */
}

.row {
    margin: 0;
    padding: 0;
}
.col-lg-1 {
    float: left;
}
</style>
