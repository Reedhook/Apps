<script>
export default {
    name: "EditVersion",
    methods: {
        validateVersion() {
            let filteredVersion = '';
            for (let i = 0; i < this.$parent.version.length; i++) {
                if (this.$parent.version.charCodeAt(i) === 46 || // Проверяем код ASCII для точки
                    (this.$parent.version.charCodeAt(i) >= 48 && this.$parent.version.charCodeAt(i) <= 57)) { // Проверяем коды ASCII для цифр 0-9
                    filteredVersion += this.$parent.version[i];
                }
            }
            this.$parent.version = filteredVersion;
        }
    }
}
</script>

<template>
    <div class="mb-3">
        <input v-model="this.$parent.version" @input="validateVersion" type="text" class="form-control mb-3" placeholder="version">
    </div>
</template>

<style scoped>

</style>
