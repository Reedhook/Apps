<script>
import api from "../../api.js";

export default {
    name: "Edit",
    data() {
        return {
            name: null,
            description: null
        }
    },
    methods: {
        getReleaseType() {
            api.get(`/api/releases_types/${this.$route.params.id}`)
                .then(res => {
                    this.name = res.data.body.release_type.name
                    this.description = res.data.body.release_type.description
                    }
                )
        },
        update(){
            api.patch(`/api/releases_types/${this.$route.params.id}`, {name: this.name, description: this.description})
                .then(res=>{
                    this.$router.push({name:'release_type.show', params: {id: this.$route.params.id}});
                })
        }
    },
    mounted() {
        this.getReleaseType()
    },
    computed:{
        isDisabled(){
            return this.name || this.description
        }
    }
}
</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="row mb-3">
            <h3>Release Type</h3>
        </div>
        <div class="w-25 border border rounded p-4">
            <div class="mb-3">
                <input type="text" v-model="name" placeholder="name" class="form-control">
            </div>
            <div class="mb-3">
                <input type="text" v-model="description" placeholder="description" class="form-control">
            </div>
            <div class="mb-3">
                <input :disabled="!isDisabled" @click.prevent="update" type="submit" value="Update" class="btn btn-primary">
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
