<script>
export default {
    name: "Edit",
    data() {
        return {
            name: null
        }
    },
    methods: {
        getPlatform() {
            axios.get(`/api/platforms/${this.$route.params.id}`)
                .then(res => {
                    this.name = res.data.body.platform.name
                    }
                )
        },
        update(){
            axios.patch(`/api/platforms/${this.$route.params.id}`, {name: this.name})
                .then(res=>{
                    this.$router.push({name:'platform.show', params: {id: this.$route.params.id}});
                })
        }
    },
    mounted() {
        this.getPlatform()
    },
    computed:{
        isDisabled(){
            return this.name
        }
    }
}
</script>

<template>
    <div class="w-25">
        <div class="mb-3">
            <input type="text" v-model="name" placeholder="name" class="form-control">
        </div>
        <div class="mb-3">
            <input :disabled="!isDisabled" @click.prevent="update" type="submit" value="Update" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
