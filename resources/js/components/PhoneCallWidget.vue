<template>
  <div v-if="displayWidget === true" class="bg-gray-200 dark:bg-gray-700 py-2 px-4 fixed rounded-lg shadow"
    style="z-index: 10; right: 20px; bottom: 90px; width: 400px">
    <div class="flex gap-2 items-center">
      <div class="flex-none">
        <a class="dark:text-gray-400" :class="{'cursor-pointer link-default': resource_url != null}" @click="visit(resource_url)">
          <div class="font-bold text-base" v-if="title != null">{{ title }}</div>
        
          <div :class="{'text-lg': title == null}">
          {{ phone }}
          </div>
        </a>
      </div>
      <div class="flex-grow text-right">
        <a class="text-center" v-if="phoneCallStarted === false" @click="startPhoneCall">
          <CallIcon />
        </a>
        <a class="text-center ml-2" @click="finishPhoneCall">
          <DeclineIcon />
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import CallIcon from "./icons/CallIcon.vue";
import DeclineIcon from "./icons/DeclineIcon.vue";
import api from "../api";

export default {
  data() {
    return {
      phoneCallStarted: false,
      displayWidget: false,
      id: null,
      title: null,
      phone: null,
      resource_url: null,
      phone_call_type: null,
    };
  },
  methods: {
    visit(url) {
      if (url != '') {
        Nova.visit(url);
      }
    },
    startPhoneCall() {
      if (this.phone_call_type === 'incoming') {
        window.zdrmWebrtcPhone.answer();
      } else {
        window.zdrmWebrtcPhone.setCallingNumber(this.phone);
        window.zdrmWebrtcPhone.callNum();
      }

      Nova.$emit("phone-call-started");
      this.phoneCallStarted = true;
      this.phone_call_type = 'incoming';
    },

    finishPhoneCall() {
      Nova.$emit("end-phone-call");
    },
  },
  /**
   * Mount the component.
   */
  mounted() {
    // Outgoing call is initiated from InitZadarmaCall component
    // Here we have all data for the call
    Nova.$on("phone-call-initiated", (data) => {
      this.displayWidget = true;

      this.id = data.id;
      this.title = data.title;
      this.phone = data.phone;
      this.resource_url = data.resource_url;
      this.phone_call_type = 'outgoing';

      // TODO: this should be in config.php to automatically start the call, or not
      this.startPhoneCall();
    });

    Nova.$on("end-phone-call", (data) => {
      if (this.phone_call_type === 'incoming') {
        zdrmWPhI.finishCall();
        window.zdrmWebPhone.cancel();
      } else {
        zdrmWPhI.finishCall();
      }
      // Reset all props to initial values when call is ended
      this.displayWidget = false;
      this.id = null;
      this.title = null;
      this.phone = null;
      this.resource_url = null;
      this.phone_call_type = null;

      // Code is taken from from zadarma-loader-phone-fn.js
      // To reset zadarma widget
      zdrmWPhI.hideCancelBtn();
      document.getElementsByClassName('zdrm-webphone-callername')[0].innerHTML = '';
      document.getElementById('zdrm-webphone-phonenumber-input').classList.remove('incoming');
    });

    // This event comes from zadarma-loader-phone-fn.js
    // Here we have only caller number, so we need to ask API
    // For resource_url and caller name
    Nova.$on("zadarma-incoming-phone-call", (data) => {
      this.displayWidget = true;
      this.phone = data.caller;
      this.phone_call_type = 'incoming';
      this.phoneCallStarted = false;

      api.getPhoneNumberInfo(data.caller).then((data) => {
        this.title = data.title;
        this.resource_url = data.resource_url;
      })
    });
  },

  components: { CallIcon, DeclineIcon },
};
</script>
