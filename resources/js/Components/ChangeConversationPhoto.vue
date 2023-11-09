<script setup>
import { ref, defineEmits, onMounted, defineProps } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { UserCircleIcon } from '@heroicons/vue/24/solid'

const {conversation} = defineProps(['conversation']);

const open = ref(true)
const selectedFile = ref(null);
const fileInputFilled = ref(false);

const emit = defineEmits(['close']);

const closeDialog = () => {
  emit('close'); 
};

const handleFileChange = () => {
    let fileInput = document.getElementById('conversation_photo');
    if (fileInput.files.length > 0) {
    fileInputFilled.value = true;
    const reader = new FileReader();
    reader.readAsDataURL(fileInput.files[0]);
    reader.onload = () => {
        selectedFile.value = reader.result;
    };
    } else {
    fileInputFilled.value = false;
    selectedFile.value = null;
    }
};

const saveConversationPhoto = () => {
    let fileInput = document.getElementById('conversation_photo');
    if (fileInput && fileInput.files.length > 0) {
        // Create a FormData object to handle the file data.
        const formData = new FormData();

        formData.append('conversation_photo', fileInput.files[0]);
        console.log(formData);

        axios.post(`/api/conversations/${conversation.conversation.id}/photo`, formData).then(response => {
            console.log(response.data);
        });
        location.reload();
    }
};

</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-40" @close="closeDialog">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <form @submit.prevent="saveConversationPhoto">
                    <div>
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full">
                            <span v-if="fileInputFilled" class="w-16 h-16 overflow-hidden bg-white rounded-full"><img :src="selectedFile" alt="Selected Photo" class="h-20 w-20 object-cover w-full h-full" /></span>
                            <span v-else-if="conversation && conversation.conversation.conversation_photo"><img class="h-15 w-15 flex-none rounded-full bg-gray-50" :src="'/conversation-photos/' + conversation.conversation.conversation_photo"></span>
                            <span v-else> <img src="../../../icons/group.png"> </span>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:flex sm:justify-center sm:gap-4">
                        <input
                            type="file"
                            class="hidden"
                            id="conversation_photo"
                            name="conversation_photo"
                            ref="fileInput"
                            accept="image/*"
                            @change="handleFileChange"
                        >
                        <button type="button" class="mt-3 sm:mt-0 inline-flex flex-1 w-full sm:w-auto justify-center rounded-md px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm bg-gray-300 hover:bg-gray-200" @click="closeDialog" ref="cancelButtonRef">
                        Cancel
                        </button>
                        <div v-if="!fileInputFilled" class="flex-1">
                            <label for="conversation_photo" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 cursor-pointer">
                            Upload Photo
                            </label>
                        </div>
                        <div v-else class="flex-1">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 cursor-pointer">
                            Save
                            </button>
                        </div>
                    </div>
                </form>

            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
