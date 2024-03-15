<script>
import api from "../../../api.js";

export default {
    name: "AddTechnicalRequirement",
    data() {
        return {
            techs: null
        }
    },
    methods:{
        showData(id){
            this.getReleasesTypes();
            this.$parent.openForm(id);
        },
        getReleasesTypes(){
            api.get('/api/techs_reqs/')
                .then(res=>{
                    this.techs = res.data.body.technicals_requirements
                })
        },
        selectTechnicalRequirement(id){
            this.$parent.selectedTechnicalRequirement = this.$parent.selectedTechnicalRequirement ===id ? null:id
        }
    }
}
</script>

<template>
    <div class="mb-3 ">
        Технические характеристики:
        <button id="add_TechnicalRequirement" @click="this.showData('#addTechnicalRequirement')" class="btn"><i class="gg-add"></i></button>
        <div   v-if="this.$parent.selectedTechnicalRequirement">
            <p class="selectedModel">
                {{ this.techs.find(tech => tech.id ===this.$parent.selectedTechnicalRequirement).os_type }}
            </p>
            <p class="selectedModel">
                {{ this.techs.find(tech => tech.id ===this.$parent.selectedTechnicalRequirement).specifications }}
            </p>
        </div>

        <div v-else>
            <p class="selectedModel">
                Технические характеристики
            </p>
        </div>
        <div id="addTechnicalRequirement" class="showForm">
            <form role="form" action="/requestFine" autocomplete="off" method="POST">
                <ul>
                    <li id="project_list" v-for="tech in this.techs" :key="tech.id"
                        class="btn"
                        :class="{ 'selected':  this.$parent.selectedTechnicalRequirement === tech.id  }"
                        @click="selectTechnicalRequirement(tech.id)">
                        {{ tech.os_type }}
                    </li>
                </ul>

                <button type="button" class="btn btn-danger" @click="this.$parent.openForm('#addTechnicalRequirement')">
                    Закрыть
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
