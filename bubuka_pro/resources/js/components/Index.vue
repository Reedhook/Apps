<script>
import Header from "./Header.vue";

export default {
    name: "Index",
    components: {Header},

}


</script>

<template>
    <Header></Header>
    <router-view :key="$route.fullPath"></router-view>
</template>

<style scoped>

</style>
