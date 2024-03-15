<script>
import {isDisabled} from "bootstrap/js/src/util/index.js";
import api from '../../api.js';
export default {
    name: "Create",
    data() {
        return {
            name: null
        }
    },
    methods: {
        store() {
            api.post('/api/platforms/', {name: this.name})
                .then(res=>{
                    this.$router.push({name:'platform.index'})
                })
        }
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
            <input :disabled="!isDisabled" @click.prevent="store" type="submit" value="Add" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
