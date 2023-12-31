<script setup>
import { ref, defineProps} from 'vue';
import { UserCircleIcon } from '@heroicons/vue/24/solid';
import InviteMember from './InviteMember.vue';
import SuccessNotification from './InvitationSentNotification.vue';

const { participants, conversationId, conversation} = defineProps(['participants', "conversationId", 'conversation']);

const inviting = ref(false);
const successInviting = ref(false);

const toggleInvitingMember = () => {
  inviting.value = !inviting.value
}

const successNotification = () => {
  successInviting.value = true;
}

</script>

<template>
  <div class="flex-col ">
    <div class="max-h-[89vh] overflow-y-auto">
      <div class="lg:flex hidden lg:px-5">
        <ul role="list" class="divide-y divide-gray-100 flex flex-col h-[83vh]">
          <li v-for="participant in participants" :key="participant.id" class="py-3">
            <div v-for="user in participant" :key="user.id" class="flex justify-between gap-x-6 py-3 w-full">
              <div class="flex min-w-0 gap-x-4 flex-col">
                <!-- participants profile pic -->
                <div v-if="user.user.profile_picture" class="w-8 h-8 overflow-hidden bg-white rounded-full">
                  <img class="h-8 w-8 flex-none rounded-full object-cover w-full h-full" :src="'/profile-picture/' + user.user.profile_picture">
                </div>
                <div v-else>
                  <UserCircleIcon class="h-9 w-9 text-gray-300"/>
                </div>
                <div class="min-w-0 flex-auto">
                  <p class="text-sm font-semibold leading-6 text-gray-900">{{ user.user.name }}</p>
                  <p class="truncate text-xs leading-5 text-gray-500">{{ user.user.email }}</p>
                </div>
              </div>
                <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                  <div :class="{ 'bg-emerald-500/20': user.user.status === 'online', 'bg-red-500/20': user.user.status === 'offline' }" class="flex-none rounded-full p-1">
                    <div :class="{ 'bg-emerald-500': user.user.status === 'online', 'bg-red-500': user.user.status === 'offline' }" class="h-1.5 w-1.5 rounded-full" />
                  </div>
                  <p :class="{ 'text-gray-500': user.user.status === 'offline' }" class="text-xs leading-5 text-gray-500">{{ user.user.status }}</p>
                </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
      <button @click="toggleInvitingMember" class="lg:mx-auto hidden lg:flex w-3/4 justify-center group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 bg-gray-100 hover:bg-gray-200 hover:text-blue-500">
        Invite Member
      </button>
  </div>

  <div v-if="inviting">
    <InviteMember 
      :conversation="conversation"
      @close="toggleInvitingMember"
      @successInviting="successNotification"
    />
  </div>

  <div v-if="successInviting">
    <SuccessNotification class="mt-12"/>
  </div>

</template>