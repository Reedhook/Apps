<script>
import api from "../../api.js";
import {isDisabled} from "bootstrap/js/src/util/index.js";

export default {
    name: "Edit",
    data() {
        return {
            os_type: null,
            specifications: null
        }
    },
    methods: {
        isDisabled,
        getProject() {
            api.get(`/api/techs_reqs/${this.$route.params.id}`)
                .then(res => {
                    this.os_type = res.data.body.technical_requirement.os_type
                    this.specifications = res.data.body.technical_requirement.specifications
                    }
                )
        },
        update(){
            api.patch(`/api/techs_reqs/${this.$route.params.id}`, {os_type: this.os_type})
                .then(res=>{
                    this.$router.push({name:'technical_requirement.show', params: {id: this.$route.params.id}});
                })
        }
    },
    mounted() {
        this.getProject()
    },
    computed:{
        isDisabled(){
            return this.os_type || this.specifications
        }
    }
}
</script>

<template>
    <div class="w-25">
        <div class="mb-3">
            <input type="text" v-model="os_type" placeholder="os_type" class="form-control">
        </div>
        <div class="mb-3">
            <input type="text" v-model="specifications" placeholder="os_type" class="form-control">
        </div>
        <div class="mb-3">
            <input :disabled="!isDisabled" @click.prevent="update" type="submit" value="Update" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
