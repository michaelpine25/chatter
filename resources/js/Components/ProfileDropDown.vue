<script setup>
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import { useRoute, useRouter } from 'vue-router';
    import { Inertia } from '@inertiajs/inertia';

    const router = useRouter();

    const logout = () => {
        Inertia.post('/logout').then(() => {
        Inertia.visit('/');
        });
    }   

</script>


<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <span class="inline-flex rounded-md">
            <!-- Show user name only on large screens (lg and up) -->
            <button
                type="button"
                class="hidden lg:inline-flex md:inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
            >
                {{ $page.props.auth.user.name }}

                <svg
                class="ml-2 -mr-0.5 h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                >
                <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                />
                </svg>
            </button>

            <!-- Show only the dropdown arrow on medium (md) and small (sm) screens -->
            <button
                type="button"
                class="lg:hidden md:hidden inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
            >
                <svg
                class="h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                >
                <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                />
                </svg>
            </button>
            </span>
        </template>

        <template #content>
            <DropdownLink :href="'/profile'"> Profile </DropdownLink>
            <div @click="logout" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none hover:cursor-pointer transition duration-150 ease-in-out">
            Log Out
            </div>
        </template>
        </Dropdown>

</template>