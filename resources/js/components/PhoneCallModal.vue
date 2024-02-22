<template>
  
    <Call />

</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from "vue";
import Call from "./Call.vue";

const observer = ref(null);
const dark = ref(false);

onMounted(() => {
  dark.value = document.documentElement.classList.contains("dark");

  observer.value = new MutationObserver((records) => {
    records.forEach((record) => {
      dark.value = record.target.classList.contains("dark");
    });
  });

  observer.value.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ["class"],
    childList: false,
    characterData: false,
  });
});

onBeforeUnmount(() => {
  observer.value.disconnect();
  observer.value = null;
});
</script>

<style>
/* Scoped Styles */
</style>
