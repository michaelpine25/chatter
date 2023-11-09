<script setup>
import ProfilePicture from '../Components/ProfilePicture.vue';
import InvitationSentNotification from '../Components/InvitationSentNotification.vue';
import ConversationSettings from '../Components/ConversationSettings.vue';
import ProfileDropdown from '../Components/ProfileDropDown.vue';
import { ref, onMounted, defineProps, defineEmits, watch, onBeforeUnmount} from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { usePage } from '@inertiajs/inertia-vue3';
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import ChatBox from '../Components/ChatBox.vue';
import InvitationDropdown from '../Components/InvitationDropdown.vue';
import { useForm } from '@inertiajs/vue3';
import { UserCircleIcon } from '@heroicons/vue/24/solid';
import {
  UsersIcon,
} from '@heroicons/vue/24/outline'

const sidebarOpen = ref(false);
const conversations = ref(null);
const creatingChat = ref(false);
const selectedConversationId = ref(null);
const messages = ref(null);
const participants = ref(null);
const currentUser = ref(null);
const conversationId = ref(null);
const viewingInvitations = ref(false);
const invitations = ref(null);
const invitationSent = ref(false);
const conversationName = ref(null);
const route = useRoute();
const router = useRouter();
const mobileSidebar = ref(false);

const getConversationData = async (conversationId) => {
  if (await userIsAuthorized(conversationId)) {

    messages.value = null;
    selectedConversationId.value = conversationId
    try {
        messages.value = await fetchMessages(conversationId);
        fetchParticipants(conversationId);
    } catch (error) {
        console.error('Error fetching messages:', error);
        // Handle the error if needed
    }
    resetUnreadCount(conversationId);
    await router.push({ name: 'conversation', params: { id: conversationId } });

  } else {
    await router.push({ name: 'dashboard'});
  }
}

const userIsAuthorized = async (conversationId) => {
  const response = await axios.get(`/api/conversation/${conversationId}/authorize`);
  return response.data
}

const form = useForm({
    name: '',
});

const fetchMessages = async (conversationId) => {
  try {
    const response = await axios.get(`/api/conversation/${conversationId}/messages`);
    return response.data;
  } catch (error) {
    console.error('Error fetching messages:', error);
    throw error;  // Re-throw the error to let the caller handle it
  }
  
};

const fetchParticipants = async (conversationId) => {
    try {
        const response = await axios.get(`/api/participants/${conversationId}`);
        participants.value = response.data;
    } catch {
        console.error('Error fetching Participants');
    }
};

const getCurrentUser = async () => {
    try {
        const response = await axios.get('/api/user');
        currentUser.value = response.data;
    } catch {
        console.log('Error fetching current user');
    }
}

const getConversations = () => {
    axios.get('/api/conversations').then((response) => {
      conversations.value = response.data;
      conversations.value = conversations.value.sort((a, b) => {
        // Check if 'last_message' exists before creating Date objects
        const aCreatedAt = a.last_message ? new Date(a.last_message.created_at) : new Date(0);
        const bCreatedAt = b.last_message ? new Date(b.last_message.created_at) : new Date(0);

        return bCreatedAt - aCreatedAt;
      });
    }).catch((error) => {
        //error
    });
}

const shortenName = (text) => {
    if (text.length > 15) {
        text = text.substring(0, 15);
        return text + '...'
      }else {
          return text ;
      }
}
const toggleCreatingChat = () => {
  creatingChat.value = !creatingChat.value; 
};

const toggleInvitationNotification = () => {
  invitationSent.value = !invitationSent.value
}

const handleCreatedConversation = (conversationId) => {
  getConversations();
  getConversationData(conversationId);
}

const resetUnreadCount = (conversationId) => {

  const conversation = conversations.value.find(conversation => conversation.id === conversationId);
  
  if (conversation) {
    // Find the participant by userId
    const participant = conversation.participants.find(participant => participant.user_id === currentUser.value.id);
    
    if (participant) {
      // Reset the unread_count to 0
      participant.unread_count = 0;
    }
  }
  
  axios.post(`/api/participants/${conversationId}/unreadCount`).then((response) => {
    //
  });
}

const getInvitations = () => {
    axios.get('/api/invitations').then((response) => {
      invitations.value = response.data;
        
    }).catch((error) => {
        console.log(error);
    });
}

const createConversation = async (name) => {
    try {
      let response = await axios.post('/api/conversations', {
        name: name
      })
      router.push({ name: 'conversation', params: { id: response.data.conversation_id } });
      form.name = '';
      toggleCreatingChat();
      getConversations();
    }
    catch (error) {
      console.log(error.response.data);
    }
}

const formatTimeAgo = (timestamp) => {
  const messageDate = new Date(timestamp); // Parse the timestamp string into a JavaScript Date object
  const currentDate = new Date();
  const timeDifference = currentDate - messageDate;
  const minutesAgo = Math.floor(timeDifference / 60000);

  if (minutesAgo < 1) {
    return 'Just now';
  } else if (minutesAgo < 60) {
    return `${minutesAgo} min${minutesAgo > 1 ? 's' : ''}`;
  } else if (minutesAgo < 1440) { // 1440 minutes in 1 day (24 hours)
    const hoursAgo = Math.floor(minutesAgo / 60);
    return `${hoursAgo} hr${hoursAgo > 1 ? 's' : ''}`;
  } else {
    const daysAgo = Math.floor(minutesAgo / 1440);
    return `${daysAgo} day${daysAgo > 1 ? 's' : ''}`;
  }
};

const lessThanHundred = (value) => {
  if (value > 99) {
    return '99+';
  }else{
    return value;
  }
}

const deleteInvite = (id) => {
    axios.delete(`/api/invitation/deny/${id}`).then((response) => {
      console.log('you succesfully denied the invitation');
  });
  const indexToDelete = invitations.value.invitations.findIndex(invitation => invitation.conversation_id === id);
  invitations.value.invitations.splice(indexToDelete, 1);
}

const acceptInvite = (id) => {
  console.log(invitations.value.invitations);
  axios.post('/api/invitation/accept', {
      conversationId: id
  }).then((response) => {
    router.push({ name: 'conversation', params: { id: id } });
    const indexToAccept = invitations.value.invitations.findIndex(invitation => invitation.conversation_id === id);
    invitations.value.invitations.splice(indexToAccept, 1);
  }).catch((error) => {
      console.error('Failed to accept invitation', error);
  });
  getConversations()
}

const listenForInvite = (currentUser) => {
  Echo.channel(`invitation-sent`)
    .listen('InvitationSent', (event) => {
      // Handle the event data, update the invitation count
      if(event.recipient.id == currentUser.id) {
        getInvitations();
      }
    });
}

const listenForInvitationAccepted = () => {
  Echo.channel(`invitation-accepted`)
    .listen('InvitationAccepted', (event) => {
      if(selectedConversationId.value == event.conversationId) {
        fetchParticipants(event.conversationId);
      }
    });
}

const listenForNewMessage = () => {
  Echo.channel(`message`)
  .listen('MessageSent', (event) => {
    if(route.params.id == event.conversationId) {
      messages.value = [...messages.value, event.message];
    }else{
      getConversations();
    }
  });
}

watch(() => route.params.id, (newConversationId) => {
  messages.value = null;
  conversationId.value = newConversationId;
  getConversationData(newConversationId);
});

onMounted( async () => {
  getConversations();
  getInvitations();
  await getCurrentUser();
  listenForInvite(currentUser.value);
  listenForInvitationAccepted();
  listenForNewMessage()

});

</script>

<template>

  <div class="flex">
    <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden top-0 right-0 px-2 py-2 bg-gray-100">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>

    <div v-if="mobileSidebar" @click="mobileSidebar = false" class="fixed top-0 left-0 right-0 bottom-0 bg-black opacity-60 z-20 transition-opacity duration-300 ease-in-out"></div>

      <!-- pop out sidebar component -->
    <div v-if="mobileSidebar">
      <div  class="fixed top-0 left-0 h-screen w-[295px] bg-white z-30 overflow-y-auto transform translate-x-0 transition-transform duration-300 ease-in-out" >
        <div class="flex-grow flex-col overflow-y-auto border-r border-gray-200 bg-white px-4 pb-20">
          <img class="h-12 w-30 mt-6 mb-6" src="../../../icons/chatter-logo.png">
          <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
              <li>
                <ul role="list" class="-mx-2 space-y-2">
                  <li v-if="creatingChat" class="border-b bg-gray-100 rounded-md py-1 transform">
                    <div class= 'group flex gap-x-2 rounded-md px-2 py-2 text-sm leading-6 font-semibold '>
                      <div class="flex items-center justify-center ml-2">
                        <img class="h-12 w-16 flex-none " src="../../../icons/group.png"> 
                      </div>
                      <form @submit.prevent=" form.name && createConversation(form.name)" class=" min-w-0 flex align-left items-start flex-col w-full">
                        <input v-model="form.name" maxlength="25" class="w-full text-md font-semibold leading-6 text-black flex rounded-lg py-1 text-sm bg-white border-none focus:coutline-none focus:ring-0" placeholder="Name your chat">
                          <div>
                            <button @click="createConversation" type="submit" class="truncate text-xs leading-5 mr-2 mt-2 rounded bg-blue-50 px-2 text-xs font-semibold text-blue-600 shadow-sm hover:bg-blue-100">Create</button>
                            <button @click="toggleCreatingChat" class="truncate text-xs leading-5 rounded bg-gray-50 px-2 text-xs font-semibold text-gray-600 shadow-sm hover:bg-gray-100">Cancel</button>
                          </div>
                      </form> 
                    </div>
                  </li>

                  <li v-for="conversation in conversations" :key="conversation.id">
                    <div :class="[selectedConversationId == conversation.id ? 'bg-blue-50' : 'text-gray-700 hover:bg-blue-50', 'flex rounded group w-full']">
                        <button @click="getConversationData(conversation.id)" class="w-full group gap-x-2 flex rounded-md px-2 py-5 text-sm leading-6 font-semibold">
                          <div v-if="conversation.conversation_photo" class="w-12 h-12 overflow-hidden bg-white rounded-full">
                            <img class="object-cover w-full h-full flex-none" :src="'/conversation-photos/' + conversation.conversation_photo"> 
                          </div>
                          <div v-else>
                            <img class="h-12 w-12 flex-none " src="../../../icons/group.png"> 
                          </div>
                          <div class="min-w-0 flex align-left items-start flex-col">
                            <div class="text-md font-semibold leading-6 text-gray-900 flex">{{ shortenName(conversation.name) }}</div>
                            <div v-if="conversation.last_message">
                              <div class="truncate text-xs leading-5 text-gray-500 max-w-[1950px]">{{conversation.last_message.user.name}}: 
                                <span v-if="conversation.last_message.content">
                                  {{shortenName(conversation.last_message.content)}}
                                </span>
                                <span v-else>
                                  Image
                                </span>
                                - {{ formatTimeAgo(conversation.last_message.created_at) }}
                              </div>
                            </div>
                      
                            <div v-else>
                              <div class="truncate text-xs leading-5 text-gray-500">No messages</div>
                            </div>
                          </div>
                          <div v-if="conversation.participants[0].unread_count > 0" class="ml-auto w-5 h-5 bg-red-500 text-white font-bold rounded-3xl text-sm flex items-center justify-center">{{lessThanHundred(conversation.participants[0].unread_count)}}</div>
                        </button>
                        <div class="hidden group-hover:block" >
                          <ConversationSettings
                            :conversation="conversation"
                            @invitationNotification="toggleInvitationNotification"             
                          />
                        </div>
                    </div>
                  </li>

                </ul>
              </li>
            </ul>
          </nav>
        </div>

        <div class="absolute bottom-4 left-6 right-6 ">
          <div v-if="!creatingChat && !selectedConversationId"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 animate-bounce mx-auto mb-2 text-blue-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <button @click="toggleCreatingChat" class="w-full justify-center group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 bg-gray-100 hover:bg-gray-200 hover:text-blue-500">
          Create New Chat!
        </button>
      </div>
    </div>

    </div>
    <!-- Static sidebar for desktop -->
    <div  class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-[295px] lg:flex-col" >
      <!-- Sidebar component, swap this element with another sidebar if you like -->
      <div class="flex-grow flex-col overflow-y-auto border-r border-gray-200 bg-white px-6 pb-20 pt-20">
        <nav class="flex flex-1 flex-col">
          <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
              <ul role="list" class="-mx-2 space-y-2">
                <li v-if="creatingChat" class="border-b bg-gray-100 rounded-md py-1 transform">
                  <div class= 'group flex gap-x-2 rounded-md px-2 py-2 text-sm leading-6 font-semibold '>
                    <div class="flex items-center justify-center ml-2">
                      <img class="h-12 w-16 flex-none " src="../../../icons/group.png"> 
                    </div>
                    <form @submit.prevent=" form.name && createConversation(form.name)" class=" min-w-0 flex align-left items-start flex-col w-full">
                      <input v-model="form.name" maxlength="25" class="w-full text-md font-semibold leading-6 text-black flex rounded-lg py-1 text-sm bg-white border-none focus:coutline-none focus:ring-0" placeholder="Name your chat">
                        <div>
                          <button @click="createConversation" type="submit" class="truncate text-xs leading-5 mr-2 mt-2 rounded bg-blue-50 px-2 text-xs font-semibold text-blue-600 shadow-sm hover:bg-blue-100">Create</button>
                          <button @click="toggleCreatingChat" class="truncate text-xs leading-5 rounded bg-gray-50 px-2 text-xs font-semibold text-gray-600 shadow-sm hover:bg-gray-100">Cancel</button>
                        </div>
                    </form> 
                  </div>
                </li>

                <li v-for="conversation in conversations" :key="conversation.id">
                  <div :class="[selectedConversationId == conversation.id ? 'bg-blue-50' : 'text-gray-700 hover:bg-blue-50', 'flex rounded group w-full']">
                      <button @click="getConversationData(conversation.id)" class="w-full group gap-x-2 flex rounded-md px-2 py-5 text-sm leading-6 font-semibold">
                        <div v-if="conversation.conversation_photo" class="w-12 h-12 overflow-hidden bg-white rounded-full">
                          <img class="object-cover w-full h-full flex-none" :src="'/conversation-photos/' + conversation.conversation_photo"> 
                        </div>
                        <div v-else>
                          <img class="h-12 w-12 flex-none " src="../../../icons/group.png"> 
                        </div>
                        <div class="min-w-0 flex align-left items-start flex-col">
                          <div class="text-md font-semibold leading-6 text-gray-900 flex">{{ shortenName(conversation.name) }}</div>
                          <div v-if="conversation.last_message">
                            <div class="truncate text-xs leading-5 text-gray-500 max-w-[1950px]">{{conversation.last_message.user.name}}: 
                              <span v-if="conversation.last_message.content">
                                {{shortenName(conversation.last_message.content)}}
                              </span>
                              <span v-else>
                                Image
                              </span>
                               - {{ formatTimeAgo(conversation.last_message.created_at) }}
                            </div>
                          </div>
                    
                          <div v-else>
                            <div class="truncate text-xs leading-5 text-gray-500">No messages</div>
                          </div>
                        </div>
                        <div v-if="conversation.participants[0].unread_count > 0" class="ml-auto w-5 h-5 bg-red-500 text-white font-bold rounded-3xl text-sm flex items-center justify-center">{{lessThanHundred(conversation.participants[0].unread_count)}}</div>
                      </button>
                      <div class="hidden group-hover:block" >
                        <ConversationSettings
                          :conversation="conversation"
                          @invitationNotification="toggleInvitationNotification"             
                        />
                      </div>
                  </div>
                </li>

              </ul>
            </li>
          </ul>
        </nav>
      </div>

      <div class="absolute bottom-4 left-6 right-6 ">
          <div v-if="!creatingChat && !selectedConversationId"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 animate-bounce mx-auto mb-2 text-blue-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3 3v-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <button @click="toggleCreatingChat" class="w-full justify-center group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 bg-gray-100 hover:bg-gray-200 hover:text-blue-500">
          Create New Chat!
        </button>
      </div>
    </div>

    <div class="flex-1">
      
      <div class="flex w-full shadow sticky top-0 z-10 flex h-16 shrink-0 items-center border-b border-gray-200 bg-white px-4 sm:gap-x-6 sm:px-6 lg:px-8 justify-between">
        <!-- "Chatter" logo to the left -->
          <img class="h-12 w-30 mt-6 mb-6 hidden lg:block md:block" src="../../../icons/chatter-logo.png">
          <img class="h-10 w-10 mt-6 mb-6 sm:hidden" src="../../../icons/comments.png">
        
        <!-- ProfileDropdown and InvitationDropdown to the right -->
        <div class="flex items-center">
          <div v-if="invitations !== null" class="mx-4 relative flex">
            
            <InvitationDropdown class="focus:outline-none focus:ring-0" :invitations="invitations" @acceptInvite="acceptInvite" @deleteInvite="deleteInvite"/>
            <div v-if="invitations.invitations && invitations.invitations.length > 0">
              <div class="w-5 h-5 bg-red-500 text-white font-bold rounded-3xl text-sm flex items-center justify-center">
                {{invitations.invitations.length}}
              </div>
            </div>
          </div>

          <!-- ProfileDropdown -->
          <div class="relative -m-1.5 flex items-center p-1.5">
            <span class="sr-only">Open user menu</span>
            <div v-if="currentUser && currentUser.profile_picture">
              <ProfilePicture class="w-10 h-10" />
              <!-- <img class="h-8 w-8 flex-none rounded-full" :src="'/profile-picture/' + profilePicturePath"> -->
            </div>
            <div v-else>
              <UserCircleIcon class="h-8 w-8 flex-none rounded-full text-gray-300" />
            </div>
            <span class=" lg:flex lg:items-center">
              <ProfileDropdown />
            </span>
          </div>
          
          <!-- InvitationDropdown -->
        </div>
      </div>


      <main class="bg-white lg:pl-72 ">
        <!-- Set a max-width for the ChatBox to take up the remaining space beside the sidebar -->
        <div class="flex justify-end">

          <div v-if="messages !== null && selectedConversationId && currentUser" class=" px-1 sm:px-1 lg:px-1 w-full">
            <!-- Your content -->
            <ChatBox
              :conversationId="selectedConversationId"
              :messages="messages"
              :participants="participants"
              :currentUser="currentUser"
              
            />
          </div>

          <div v-else class="flex items-center justify-center w-full h-screen">
            <div class="flex-col justify-center items-center w-full">
              <img class="h-20 w-20 mx-auto" src="../../../icons/comments.png">
              <h3 class="w-full text-center py-6 text-xl font-extrabold text-black">Click Create New Chat and start chatting!</h3>
            </div>
          </div>

          <div v-if="invitationSent">
            <InvitationSentNotification class="mt-12" />
          </div>
        </div>
      </main> 
    </div>
  </div>

</template>

