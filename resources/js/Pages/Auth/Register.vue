<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserCircleIcon } from '@heroicons/vue/24/solid';
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const form = useForm({
    profile_picture: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
const selectedFile = ref(null);
const fileInputFilled = ref(false);
const router = useRouter();

const submit = () => {
    const formData = new FormData();

    formData.append('name', form.name);
    formData.append('email', form.email);
    formData.append('password', form.password);
    formData.append('password_confirmation', form.password_confirmation);

    let fileInput = document.getElementById('profile_picture');
    if (fileInput && fileInput.files.length > 0) {
        formData.append('profile_picture', fileInput.files[0]);
    }

    axios.post(route('register'), formData)
        .then((response) => {
            form.reset('password', 'password_confirmation');
            location.reload();
        })
        .catch((error) => {
            console.log(error);
        });
};


const handleProfilePictureUpload = () => {
    let fileInput = document.getElementById('profile_picture');
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
}

</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div class="mb-3 text-center flex-col ">
                <span v-if="fileInputFilled" class="flex justify-center mb-2 overflow-hidden h-24 w-24 bg-white rounded-full mx-auto"><img :src="selectedFile" alt="Selected Photo" class="h-24 w-24 object-cover" /></span>
                <span v-else> <UserCircleIcon class="w-24 h-24 mx-auto text-gray-300 dark:text-gray-300" /> </span>
                <label for="profile_picture" class="cursor-pointer text-blue-500 hover:text-blue-700">
                    Upload Profile Picture
                    <input
                        id="profile_picture"
                        type="file"
                        class="hidden"
                        accept="image/*"
                        @change="handleProfilePictureUpload"
                    />
                </label>
            </div>

            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
