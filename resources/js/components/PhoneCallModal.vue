<template>
    <Modal :show="show" maxWidth="screen-md" tabindex="-1" role="dialog">
        <div class="bg-gray-100 dark:bg-gray-700 py-8">
            <div class="flex flex-col text-center gap-2">
                <span class="font-semibold text-base text-gray-200">Bartłomiej Gajda</span>
                <span class="text-gray-400"
                    ><a href="tel:123456789" class="text-blue-500 hover:underline"
                        >+48 795 775 257</a
                    ></span
                >
                <span class="text-gray-400">Duration: 2m 21s</span>
            </div>

            <div>
                <div class="flex flex-col items-end">
                    <div class="py-4 px-5 my-1 mx-4 dark:bg-gray-900 rounded-full text-white">
                        <div class="flex">
                            <p class="italic text-gray-300 text-left text-xxs mr-3">@Dominik</p>
                            <p class="flex-auto italic text-gray-300 text-right text-xxs">63s</p>
                        </div>

                        <p>Zastanawia się nad Fairytalem</p>
                    </div>
                    <div class="py-4 px-5 my-1 mx-4 dark:bg-gray-900 rounded-full text-white">
                        <div class="flex">
                            <p class="italic text-gray-300 text-left text-xxs mr-3">@Dominik</p>
                            <p class="flex-auto italic text-gray-300 text-right text-xxs">2s</p>
                        </div>
                        <p>do 1500zł</p>
                    </div>


            </div>

            <div class="flex flex-col items-end mt-3">
                <input
                    type="text"
                    placeholder="Notatka"
                    class="w-1/2 mx-4 form-control form-input form-input-bordered"
                />
                </div>
            </div>



            <div class="grid grid-cols-2 gap-4 mx-12 mt-6">
                <a class="text-center" id="make-call" @click="makeCall">
                    <svg
                        style="width: 100px; height: 100px; color: green"
                        xmlns="http://www.w3.org/2000/svg"
                        class="inline"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 3h5m0 0v5m0-5l-6 6M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"
                        /></svg
                ></a>
                <a class="text-center" @click="decline"
                    ><svg
                        style="width: 100px; height: 100px; color: red"
                        xmlns="http://www.w3.org/2000/svg"
                        class="inline"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"
                        /></svg
                ></a>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref } from 'vue'

function decline(event) {
    zdrmWebPhone.finishCall()
}

function makeCall(event) {
    //zdrmWebPhone.call('+48795775257')
}
</script>
