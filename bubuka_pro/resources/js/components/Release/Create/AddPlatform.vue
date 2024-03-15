<script>
export default {
    name: "AddPlatform",
    methods: {
        selectPlatform(id) {
            if (this.$parent.selectedPlatform === id) {
                this.$parent.selectedPlatform = null;
            } else {
                this.$parent.selectedPlatform = id;
            }
        }
    }
}
</script>

<template>
    <div class="mb-3">
        Платформа:
        <div
            v-if="this.$parent.selectedProject && this.$parent.projects.find(project => project.id === this.$parent.selectedProject).platforms.length > 0">
            <p class="selectedModel btn"
               v-for="platform in this.$parent.projects.find(project => project.id === this.$parent.selectedProject).platforms"
               :key="platform.id"
               :class="{ 'selected':  this.$parent.selectedPlatform === platform.id  }"
               @click="selectPlatform(platform.id)">
                {{ platform.name }}
            </p>
        </div>
        <div v-else>
            <p class="selectedModel">
                Нет платформы
            </p>
        </div>
    </div>
</template>

<style scoped>

</style>
