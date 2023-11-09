<script setup>
import { ref, defineEmits } from 'vue'
import { useForm } from '@inertiajs/vue3';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { CheckIcon } from '@heroicons/vue/24/outline'
import { useRoute, useRouter } from 'vue-router';

const emit = defineEmits(['close', 'createConversation']);
const router = useRouter();

const form = useForm({
    name: '',
});

const open = ref(true)

const closeDialog = () => {
  emit('close'); // Emit a close event
};

const createConversation = (name) => {
    axios.post('/api/conversations', {
        name: name
    }).then((response) => {
      router.push({ name: 'conversation', params: { id: response.data.conversation_id } });
      emit('createConversation');
    })
    form.name = '';
}
</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-10" @close="closeDialog">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6">

                <form @submit.prevent=" form.name && createConversation(form.name)">
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                            <CheckIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
                        </div>

                        <div class="mt-3 text-center sm:mt-5">
                            <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Name yout chat!</DialogTitle>
                            <div class="mt-2">
                                <input v-model="form.name" maxlength="25" placeholder="Chat name..." class="flex-grow p-2 rounded border-gray-200 shadow-lg mr-5 w-full"> 
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" @click="open = false">Create!</button>
                    </div>
                </form>

            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

