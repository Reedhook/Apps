<script>
import api from "../../../api.js";

export default {
    name: "EditChangelog",
    data() {
        return {
            changelogs: null
        }
    },
    methods: {
        showData(id) {
            this.$parent.openForm(id);
        },
        getChangelogs() {
            api.get('/api/changes/')
                .then(res => {
                    this.changelogs = res.data.body.changelogs
                })
        },
        selectChangelog(id) {
            if (this.$parent.selectedChangelog === id) {
                this.$parent.selectedChangelog = null;
            } else {
                this.$parent.selectedChangelog = id;
            }
        }
    },
    created() {
        this.getChangelogs();
    }
}
</script>

<template>
    <div class="mb-3">
        Выберите тугрик:
        <button id="add_Changelog" @click="this.showData('#addChangelog')" class="btn"><i class="gg-add"></i></button>
        <div v-if="this.$parent.selectedChangelog">
            <p class="selectedModel">
                {{ this.changelogs.find(changelog => changelog.id === this.$parent.selectedChangelog).changes }}
            </p>
            <p class="selectedModel">
                {{ this.changelogs.find(changelog => changelog.id === this.$parent.selectedChangelog).news }}
            </p>
        </div>
        <div v-else>
            <p class="selectedModel">
                Тугрик
            </p>
        </div>
        <div id="addChangelog" class="showForm">
            <form role="form" action="/requestFine" autocomplete="off" method="POST">
                <ul>
                    <li id="project_list" v-for="changelog in this.changelogs" :key="changelog.id"
                        class="btn"
                        :class="{ 'selected':  this.$parent.selectedChangelog === changelog.id  }"
                        @click="this.selectChangelog(changelog.id)">
                        {{ changelog.changes }}
                    </li>
                </ul>
                <button type="button" class="btn btn-danger" @click="this.$parent.openForm('#addChangelog')">
                    Закрыть
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
