<template>
  <div
    v-if="displayWidget === true"
    class="bg-gray-200 dark:bg-gray-700 py-2 px-4 fixed rounded-lg shadow"
    style="z-index: 10; right: 20px; bottom: 100px; width: 400px"
  >
    <div class="flex gap-2 items-center">
      <div class="flex-none">
        <span class="dark:text-gray-400"
          ><span class="font-bold">{{ title }}</span>
          <br />
          {{ phone }}</span
        >
      </div>
      <div class="flex-grow text-right">
        <a
          class="text-center"
          v-if="phoneCallStarted === false"
          @click="startPhoneCall"
        >
          <CallIcon />
        </a>
        <a
          class="text-center"
          v-if="phoneCallStarted === true"
          @click="finishPhoneCall"
        >
          <DeclineIcon />
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import CallIcon from "./icons/CallIcon.vue";
import DeclineIcon from "./icons/DeclineIcon.vue";

export default {
  data() {
    return {
      phoneCallStarted: false,
      displayWidget: false,
      id: "",
      title: "",
      phone: "",
    };
  },
  methods: {
    startPhoneCall() {
      window.zdrmWebrtcPhone.setCallingNumber(this.phone)
      window.zdrmWebrtcPhone.callNum()

      Nova.$emit("phone-call-started");
      this.phoneCallStarted = true;
    },

    finishPhoneCall() {
      window.zdrmWebrtcPhone.finishCall();
      Nova.$emit("phone-call-ended");
      this.phoneCallStarted = false;
      this.displayWidget = false;
    },
  },
  /**
   * Mount the component.
   */
  mounted() {
    Nova.$on("phone-call-initiated", (data) => {
      this.displayWidget = true;

      this.id = data.id;
      this.title = data.title;
      this.phone = data.phone;

      this.startPhoneCall();
    });
  },

  components: { CallIcon, DeclineIcon },
};
</script>
