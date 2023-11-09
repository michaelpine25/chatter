<script setup>
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
import { PaperClipIcon } from '@heroicons/vue/20/solid'
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia-vue3';
import { ref, onMounted, defineProps, defineEmits} from 'vue';
import { CheckCircleIcon } from '@heroicons/vue/20/solid'
import { useRoute, useRouter } from 'vue-router';

const { invitations } = defineProps(['invitations']);
const emit = defineEmits(['acceptInvite', 'deleteInvite']);

const router = useRouter();


const acceptInvitation = (conversationId) => {
    emit('acceptInvite', conversationId);
}

const deleteInvitation = (conversationId) => {
  emit('deleteInvite', conversationId);
}

const shortenName = (text) => {
  if (text.length > 15) {
      text = text.substring(0, 15);
      return text + '...'
  }else {
      return text ;
  }
}

</script>


<template>
  <Popover class="relative">
    <PopoverButton class="inline-flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900 ">
      <span><img class="w-6 h-6 mt-2" src="../../../icons/invitation.png"></span>
    </PopoverButton>

    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
      <PopoverPanel class=" max-h-80 absolute mt-5 flex w-screen max-w-max -translate-x-1/2 px-4">
        <div class="w-screen mr-20 max-w-md overflow-y-auto flex-auto rounded-3xl bg-white px-3 py-4 text-sm leading-6 shadow-lg ring-1 ring-gray-900/5">
          <div v-if="invitations.invitations && invitations.invitations.length > 0">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                      <td class="px-3 py-2 font-bold">Invited by</td>
                      <td class="px-3 py-2 font-bold">Chat Name</td>
                    </tr>
                </thead>

                <tbody>
                    <template v-for="invitation in invitations" :key="invitation.id">
                      <tr v-for="thisInvitation in invitation" :key="thisInvitation.id">
                        <td class="px-3 py-3 border-t">{{ shortenName(thisInvitation.sender.name) }}</td>
                        <td class="px-3 py-3 border-t">{{ shortenName(thisInvitation.conversation.name) }}</td>
                        <td class="px-3 py-3 border-t flex gap-x-1 justify-end">
                          <button @click="acceptInvitation(thisInvitation.conversation.id)" class="rounded-md bg-blue-500 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 ">
                            Confirm
                          </button>
                          <button @click="deleteInvitation(thisInvitation.conversation.id)" class="rounded-md bg-gray-200 px-2 py-1 text-sm font-semibold text-black shadow-sm hover:bg-gray-300 ">
                            Delete
                          </button>
                        </td>
                      </tr>
                    </template>
                </tbody>
            </table>
          </div>
          <div v-else>No invitations</div>
        </div>
      </PopoverPanel>
    </transition>
  </Popover>
</template>