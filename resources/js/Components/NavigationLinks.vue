<script setup>
    import NavLink from '@/Components/NavLink.vue';
</script>

<template>
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <div class="relative flex flex-1 justify-center" action="#" method="GET">
            <div class="hidden space-x-20 sm:-my-px sm:ml-10 sm:flex">      
                <router-link :to="{ name: 'conversation', params: { id: 1 } }" v-slot="{ isActive }">
                    <img class="w-8 h-8 mt-5" src="../../../icons/conversation.png" :class="{ 'bg-blue-300': isActive }">
                </router-link>
            </div>
        </div>
    </div>
</template>