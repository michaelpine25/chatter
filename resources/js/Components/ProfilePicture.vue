<script setup>
import {ref, onMounted} from 'vue';


const profilePicturePath = ref(null);

const getProfilePicture = async () => {
    try {
        const response = await axios.get('/api/user');
        profilePicturePath.value = response.data.profile_picture
        console.log('profile picture fetched succesfully');
    } catch {
        console.log('Error fetching profile picture');
    }
}

onMounted(() => {
    getProfilePicture()
  });
</script>

<template>
    <div class=" overflow-hidden bg-white rounded-full">
        <img class="object-cover w-full h-full flex-none" :src="'/profile-picture/' + profilePicturePath">
    </div>
</template>