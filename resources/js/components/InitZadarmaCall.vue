<template></template>
<script>
/**
 * This component just emit an event to start a phone call and close itself.
 * Rest of logic is handled by other components.
 * It is necessary because Nova does not have something like Action::emit() in PHP for emitting events to JavaScript.
 */
export default {
  watchEffect() {},
  mounted() {
    Nova.$emit("phone-call-initiated", {
      phone: this.data.phone,
      title: this.data.title,
      id: this.data.id,
    });

    this.$emit("close");
  },
  components: {},
  props: {
    data: { phone: String, title: String, id: String },
  },
};
</script>
