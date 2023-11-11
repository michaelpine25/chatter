<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import { useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/24/outline'

const { conversation } = defineProps(['conversation']);
const emit = defineEmits(['close', 'successInviting']);
const responseError = ref(null);
const conversationId = conversation.id;
const conversationName = conversation.name;

const form = useForm({
    recipientEmail: '',
});

const open = ref(true)

const closeDialog = () => {
  emit('close'); // Emit a close event
};

const invite = (conversationId, recipientEmail) => {
  responseError.value = null;
  axios.post(`/api/invitations/${conversationId}`, {
      recipientEmail: recipientEmail
  })
  .then(() => {
    open.value = false;
    emit('successInviting');
    responseError.value = null;
    form.recipientEmail = ''; 
  })
  .catch(error => {
    open.value = true
    console.error('Error sending invitation:', error);
    responseError.value = error.response ? error.response.data : 'Unknown error';
  });
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
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6">

                <form @submit.prevent=" form.recipientEmail && invite(conversationId, form.recipientEmail)">
                    <div>
                        <div class="mx-auto flex items-center justify-center rounded-full">
                          <span v-if="conversation.conversation_photo" class="w-16 h-16 overflow-hidden bg-white rounded-full"><img class="object-cover w-full h-full" :src="'/conversation-photos/' + conversation.conversation.conversation_photo"></span>
                          <span v-else class="w-16 h-16 rounded-full"><img src="../../../icons/group.png"></span>
                        </div>

                        <div class="mt-3 text-center sm:mt-5">
                            <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Invite someone to {{conversationName}}</DialogTitle>
                            <div class="mt-2">
                                <input v-model="form.recipientEmail" placeholder="Enter email..." class="w-full text-md font-semibold leading-6 text-black flex rounded-lg py-1 text-sm bg-gray-100 border-none focus:coutline-none focus:ring-0" > 
                                <div v-if="responseError !== null" class="text-red-500 pt-2">Error: {{ responseError.error }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <button 
                          type="submit" 
                          class="inline-flex w-full justify-center rounded-3xl bg-blue-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" 
                          
                        >Invite!</button>
                    </div>
                </form>

            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

