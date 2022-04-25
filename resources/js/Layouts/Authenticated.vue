<script setup>
    import SideBar from '@/Components/SideBar.vue';

</script>
<template>
    <div>
        <div>

            <div class="w-full h-screen">
                <div class="flex h-full">
                    <SideBar />
                    <!-- Page Content -->
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
