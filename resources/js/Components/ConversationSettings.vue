<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid'
import {defineEmits, defineProps, ref} from 'vue';
import InviteMember from './InviteMember.vue';
import InvitationSentNotification from './InvitationSentNotification.vue';
import UpdatePhoto from './ChangeConversationPhoto.vue';
import LeaveChat from './LeaveChat.vue';

const conversation = defineProps(['conversation']);
const emit = defineEmits([ 'getConversations'])

const inviting = ref(false);
const updatingPhoto = ref(false);
const leavingChat = ref(false);

const toggleUpdatingPhoto = () => {
  updatingPhoto.value = !updatingPhoto.value;
}

const toggleLeave = () => {
  leavingChat.value = !leavingChat.value;
}


</script>

<template>

  <Menu as="div" class="relative inline-block text-left">
    <div>
      <MenuButton class="flex items-center text-gray-400 hover:text-black absolute -top-4 -right-1">
        <span class="sr-only">Open options</span>
        <EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
      </MenuButton>
    </div>

    <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
      <MenuItems class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
        <div class="py-1">
          <MenuItem >
            <button @click="toggleUpdatingPhoto" class="block px-4 py-2 text-sm w-full hover:bg-gray-100 text-left">Edit Group Picture</button>
          </MenuItem>
          <MenuItem >
            <button @click="toggleLeave" class="block px-4 py-2 text-sm w-full hover:bg-gray-100 text-left">Leave Chat</button>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>

    <div v-if="updatingPhoto">
      <UpdatePhoto
        :conversation="conversation"
        @close="toggleUpdatingPhoto"
      />
    </div>

    <div v-if="leavingChat">
      <LeaveChat 
        @close="toggleLeave"
        :conversation="conversation"
      />
    </div>

</template>

