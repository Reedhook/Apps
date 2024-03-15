<script>
import api from "../../api.js";

export default {
    name: "Edit",
    data() {
        return {
            news: null,
            changes: null
        }
    },
    methods: {
        getChangelog() {
            api.get(`/api/changes/${this.$route.params.id}`)
                .then(res => {
                    this.news = res.data.body.changelog.news
                    this.changes = res.data.body.changelog.changes
                    }
                )
        },
        update(){
            api.patch(`/api/changes/${this.$route.params.id}`, {name: this.name})
                .then(res=>{
                    this.$router.push({name:'changelog.show', params: {id: this.$route.params.id}});
                })
        }
    },
    mounted() {
        this.getChangelog()
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
            <input type="text" v-model="news" placeholder="news" class="form-control">
            <input type="text" v-model="changes" placeholder="changes" class="form-control">
        </div>
        <div class="mb-3">
            <input :disabled="!isDisabled" @click.prevent="update" type="submit" value="Update" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
