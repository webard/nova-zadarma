<template>
  <div
    v-if="phoneCallStarted === true"
    @click="showPhoneCallModal"
    class="h-9 min-w-9 relative cursor-pointer inline-flex items-center justify-center text-red-500 dark:text-red-400 hover:[&:not(:disabled)]:text-primary-500"
  >
    <Icon
      type="phone-outgoing"
      class="inline inline-flex items-center justify-center"
    />
  </div>
</template>

<script>
export default {
  data() {
    return {
      phoneCallStarted: false,
    };
  },

  mounted() {
    Nova.$on("phone-call-started", () => {
      this.phoneCallStarted = true;
    });

    Nova.$on("phone-call-ended", () => {
      alert('zadzialalo?');
      this.phoneCallStarted = false;
    });
  },
  components: {},

  methods: {
    showPhoneCallModal: function () {
      this.$emit("show-phone-call-modal");
    },
  },
};
</script>
