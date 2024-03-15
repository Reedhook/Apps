<script>
export default {
    name: "AddFile",
    methods:{
        handleFileUpload(event) {
            this.$parent.file = event.target.files[0]; // Получаем первый выбранный файл
        },

    }
}
</script>

<template>
    <div class="mb-3">
        <input type="file" @change="handleFileUpload" class="form-control mb-3">
    </div>
</template>

<style scoped>

</style>
