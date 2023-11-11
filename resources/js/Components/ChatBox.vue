<script setup>
import { ref, onMounted, defineProps, watch, nextTick} from 'vue';
import { useForm } from '@inertiajs/vue3';
import InviteMember from './InviteMember.vue';
import InvitationSentNotification from './InvitationSentNotification.vue';
import Participants from './Participants.vue';
import { UserCircleIcon } from '@heroicons/vue/24/solid';
import ViewImage from './ViewImage.vue';

const { conversationId, messages, participants, currentUser, conversation } = defineProps(['conversationId', 'messages', 'participants', 'currentUser', 'conversation']);
const chatContentRef = ref(null);
const file = ref(null);
const imageInput = ref(null);
const viewingImage = ref(false);
const imagePath = ref(null);

const form = useForm({
  content: '',
  image: null,
  imageURL: null
});

const scrollToBottom = () => {
  // Use nextTick to wait for the DOM to update
  nextTick(() => {
    if (chatContentRef.value) {
      chatContentRef.value.scrollTop = chatContentRef.value.scrollHeight;
    }
  });
};

const openImageUpload = () => {
  //document.getElementById('image').click();
  imageInput.value.click();
};

const handleImageUpload = (event) => {
  file.value = event.target.files[0];
  if (file) {
    form.image = file.value;
    form.imageURL = URL.createObjectURL(file.value); // Create a URL for the preview
  }
  scrollToBottom();
};


const sendMessage = async (conversationId, content) => {

  const formData = new FormData();
  let imageInput = document.getElementById('image');

  if (imageInput && imageInput.files.length > 0) {
    // Append the image to the FormData object.
    formData.append('image', imageInput.files[0]);
  }
  formData.append('content', content);

  try {
    const response = await axios.post(`/api/conversation/${conversationId}/message`, formData, {
      //
    });
    form.content = '';
    form.image = null;
  } catch (error) {
    console.error('Error sending message:', error);
  }
  scrollToBottom();
};

const clearImage = () => {
  file.value = null;
  form.image = null;
  form.imageURL = null;
  imageInput.value.value = null;
}

const viewImage = (messageURL) => {
  imagePath.value = messageURL;
  viewingImage.value = true
}

const closeDialog = () => {
  viewingImage.value = false;
}

const isSent = (message, currentUserId) => {
  if(message.user_id == currentUserId) {
    return true
  }else {
    return false
  }
}

const submitForm = (conversationId, content) => {
  sendMessage(conversationId, content);
}

onMounted(() => {
  scrollToBottom();
});


</script>

<template>
<div class="flex justify-end">
  <div class="flex flex-col w-full">
    <div class=" bg-white border-r flex-1">
      <div ref="chatContentRef" class="h-[82vh] 2xl:h-[88vh] overflow-y-auto">
        <!-- Chat content goes here -->
        <div v-for="message in messages" :key="message.id" :class="isSent(message, currentUser.id) ? 'flex justify-end mx-2 py-1' : 'flex justify-start mx-2 py-1'">
          <div v-if="isSent(message, currentUser.id)" class=" max-w-[70%] flex items-end flex-col">
            <div v-if="message.image_url" class="py-2"><button @click="viewImage(message.image_url)"><img class="w-24 md:w-32 lg:w-48 rounded-2xl flex-none border border-blue-500" :src="'/picture-messages/' + message.image_url"></button></div>
            <div v-if="message.content" class="flex justify-between bg-blue-500 text-white rounded-3xl py-2 px-3 text-sm md:text-md lg:text-md 2xl:text-2xl">{{ message.content }}</div>
          </div>
          <div v-else class="flex flex-col max-w-[70%] items-start">
            <div class="flex items-center">
              <span v-if="message.user.profile_picture" class="w-10 h-10 overflow-hidden bg-white rounded-full mr-3"><img class="object-cover w-full h-full" :src="'/profile-picture/' + message.user.profile_picture"></span>
              <span v-else> <UserCircleIcon class="w-12 h-12 flex-none rounded-full bg-gray-50 mr-3 text-gray-300"/> </span>
              <div v-if="message.image_url" class="py-3"><button @click="viewImage(message.image_url)"><img class="w-24 md:w-32 lg:w-48  flex-none rounded-2xl border border-gray-300" :src="'/picture-messages/' + message.image_url"></button></div>
              <div v-if="message.content" class="bg-gray-200 text-black rounded-3xl py-2 px-3 text-sm md:text-md lg:text-md 2xl:text-2xl">{{ message.content }}</div>
            </div>
          </div>
        </div>
        <div :class="form.image ? 'w-10 h-20 w-full mt-5' : ''"></div>
      </div>

      <!-- Input and Send button -->
        <div class="w-full relative mb-2">
          <form @submit.prevent="(form.content || form.image) && sendMessage(conversationId, form.content)" @keydown.enter.prevent="submitForm(conversationId, form.content)" class="flex items-center mt-12">
              <div class="flex w-full absolute bottom-0 items-end">
                <button for="image" @click="openImageUpload" class="px-3 pt-3">
                  <img class="h-5 w-6" src="../../../icons/image.png">
                </button>
                <div class="flex-col flex-grow p-1 w-full bg-gray-100">
                  <div class="relative">
                    <span v-if="form.image"><button @click="clearImage" class=" absolute top-0"><img src="../../../icons/delete.png" class="h-5 w-5 hover:scale-125 transition-transform"></button></span>
                    <img v-if="form.image" :src="form.imageURL"  alt="Image Preview" class="max-w-[80px] max-h-[80px] rounded py-2 px-2">
                  </div>
                  <input type="file" id="image" ref="imageInput" name="image" accept="image/*" style="display:none" @change="handleImageUpload" class="flex-grow p-2 w-full bg-gray-100 mr-1 ml-2 border-none focus:coutline-none focus:ring-0">
                  <input v-model="form.content" id="content" class="py-1 px-2 bg-gray-100 border-none focus:coutline-none focus:ring-0 w-full" placeholder="Aa">
                </div> 
                <button type="submit" class=" px-3 py-2 ">
                  <img class="h-6 w-6" src="../../../icons/send-message.png"> 
                </button>
              </div>
          </form>
        </div>
    </div>
    <div v-if="viewingImage">
      <ViewImage 
        :imagePath="imagePath"
        @close="closeDialog"
      />
    </div>

    <!-- <img v-if="form.image" :src="form.imageURL" alt="Image Preview" class="w-32 h-32 rounded mt-2"> -->
  </div>
      <Participants
      :participants="participants"
      :conversationId="conversationId"
      :conversation="conversation"
    />
</div>

</template>



