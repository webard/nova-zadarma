<template></template>

<script>
import { ref, watchEffect } from "vue";

export default {
  watchEffect() {
    console.log("???");
  },
  mounted() {
    console.log("Phone call initiated");

    Nova.$emit("phone-call-initiated", {
      phone: this.data.phone,
      firstname: this.data.firstname,
      lastname: this.data.lastname,
      id: this.data.id,
    });

    this.$emit("close");
  },
  components: {},
  props: {
    data: { phone: String, firstname: String, lastname: String, id: Number },
  },
};
</script>

<style>
/* Scoped Styles */
</style>
