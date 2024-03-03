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
            <form ref="feedbackForm" v-on:submit="handleSubmit">
              <ModalHeader>Phone Call Feedback</ModalHeader>
              <ModalContent>
               
                <div class="action"><div class="md:flex-row space-y-2 md:space-y-0 py-4 md:flex-col md:space-y-2 flex w-full"><div class="!px-3 md:!px-6 w-full px-6 md:px-8"><label for="message-default-textarea-field" class="inline-block leading-tight space-x-1 flex"><span>Your feedback</span><span class="text-red-500 text-sm">*</span></label></div><div class="w-full space-y-2 !px-3 md:!px-4"><div class="space-y-1"><textarea rows="3" class="block w-full form-control form-input form-control-bordered py-3 h-auto" placeholder="Message" id="message-default-textarea-field" name="message" maxlength="-1"></textarea><!----></div><!----><!----></div></div></div>

                <div class="action"><div class="md:flex-row space-y-2 md:space-y-0 py-4 md:flex-col md:space-y-2 flex w-full"><div class="!px-3 md:!px-6 w-full px-6 md:px-8"><label for="message-default-textarea-field" class="inline-block leading-tight space-x-1 flex"><span>Rating</span><span class="text-red-500 text-sm">*</span></label></div><div class="w-full space-y-2 !px-3 md:!px-4"><div class="space-y-1"><StarRating star-size="30" @update:rating ="setRating" /></div><!----><!----></div></div></div>

                <div class="action"><div class="md:flex-row space-y-2 md:space-y-0 py-4 md:flex-col md:space-y-2 flex w-full"><div class="!px-3 md:!px-6 w-full px-6 md:px-8"><label for="template-default-select-field" class="inline-block leading-tight space-x-1 flex"><span>Conclusion</span><span class="text-red-500 text-sm">*</span></label></div><div class="w-full space-y-2 !px-3 md:!px-4"><div class="flex relative w-full"><select id="template" name="template" class="w-full block form-control form-control-bordered form-input"><option disabled="" value="">Choose an option</option></select><svg class="shrink-0 text-gray-700 pointer-events-none absolute right-[11px] top-[15px]" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6"><path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path></svg></div><!----><!----></div></div></div>
              </ModalContent>
              <ModalFooter>
                <div class="ml-auto"><button type="submit" class="border text-left appearance-none cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 disabled:cursor-not-allowed inline-flex items-center justify-center shadow h-9 px-3 bg-primary-500 border-primary-500 hover:[&:not(:disabled)]:bg-primary-400 hover:[&:not(:disabled)]:border-primary-400 text-white dark:text-gray-900">Zapisz</button></div>
              </ModalFooter>
            </form>
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
import StarRating from 'vue-star-rating'
import { saveFeedback } from '../../api';

export default {
  components: {
    Button,
    ModalHeader,
    ModalContent,
    ModalFooter,
    StarRating
  },
  props: {
    tokenName: {
      required: true,
      type: String,
    },
    rating: {
      required: true,
      type: Number,
    },
    show: { type: Boolean, default: false },
  },
  emits: ["confirmed", "cancel"],
  methods: {
    handleConfirmed() {
      this.$emit("confirmed");
    },

    setRating(rating) {
      this.$props.rating = rating
    },

    handleSubmit() {

      const form = this.$refs.feedbackForm;
      const formData = new FormData(form);
      
      console.log('Message: '+formData.get('message'));
      console.log('Rating: '+this.$props.rating);

      this.$props.show = false;
    },
  },
};
</script>
