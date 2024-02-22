<template>
    <Modal
    :show="show"
    @modal-close="handleClose"
    maxWidth="screen-md"
    tabindex="-1"
    role="dialog"
  >
  <div class="bg-gray-100 dark:bg-gray-700 py-8">
    <div class="flex flex-col text-center gap-2">
      <span class="font-semibold text-base text-gray-200"
        >Bartłomiej Gajda</span
      >
     
    </div>

    <div class="grid grid-cols-2 gap-4 mx-12 mt-6">
      <a class="text-center" id="make-call" @click="makeCall">
        Call
        <CallIcon />
      </a>
      <a class="text-center" @click="stopCall">
        <DeclineIcon />
    </a>
    </div>
    <div class="bg-30 px-6 py-3 flex">
      <div class="ml-auto">
        <button
          type="button"
          data-testid="cancel-button"
          dusk="cancel-general-button"
          @click.prevent="handleClose"
          class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
        >
          {{ __("Cancel") }}
        </button>
      </div>
    </div>
  </div>
</Modal>
</template>

<script>
import CallIcon from "./icons/CallIcon.vue";
import DeclineIcon from "./icons/DeclineIcon.vue";

export default {
  methods: {
    handleClose() {
      this.$emit("close");
    },
    handleConfirm() {
      this.$emit("confirm");
    },
    makeCall() {
      window.zdrmWebrtcPhone.setCallingNumber('+48795775257')
      window.zdrmWebrtcPhone.callNum()
    },

    stopCall() {
      window.zdrmWebrtcPhone.finishCall()
    },
  },
  /**
   * Mount the component.
   */
  mounted() {
    this.$refs.confirmButton.focus();
  },

  components: { CallIcon, DeclineIcon }
};
</script>
