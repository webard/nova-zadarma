<template>
  <teleport to="body">
    <template v-if="show">
      <div class="webard-nova-zadarma">
        <div
          v-bind="defaultAttributes"
          class="modal fixed inset-0 z-[60] px-3 md:px-0 py-3 md:py-6 overflow-x-hidden overflow-y-auto"
          :data-modal-open="show"
          :aria-modal="show"
          role="dialog"
        >
          <div
            class="@container/modal relative mx-auto z-20 max-w-2xl text-left"
            :class="contentClasses"
            ref="modalContent"
          >
            <div
              class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
            >
              <ModalHeader>Phone Call Feedback</ModalHeader>
              <ModalContent>
                <p class="leading-tight">sdfsd</p>
              </ModalContent>
              <ModalFooter>
                <div class="ml-auto">sdf</div>
              </ModalFooter>
            </div>
          </div>
        </div>

        <div
          class="fixed inset-0 z-[55] bg-gray-500 dark:bg-gray-900 opacity-75"
          dusk="modal-backdrop"
        />
      </div>
    </template>
  </teleport>
</template>

<script>
import { Button } from "laravel-nova-ui";
import ModalHeader from "./ModalHeader";
import ModalContent from "./ModalContent";
import ModalFooter from "./ModalFooter";

export default {
  components: {
    Button,
    ModalHeader,
    ModalContent,
    ModalFooter,
  },
  props: {
    tokenName: {
      required: true,
      type: String,
    },
    show: { type: Boolean, default: false },
  },
  emits: ["confirmed", "cancel"],
  methods: {
    handleConfirmed() {
      this.$emit("confirmed");
    },
  },
};
</script>
