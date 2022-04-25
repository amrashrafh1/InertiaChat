<script setup>
    import Messages from './Messages.vue'

</script>
<template>
    <div class="sidebar hidden lg:flex w-1/3 flex-2 flex-col pr-6">
        <div class="search flex-2 pb-6 px-2">
            <input type="text" class="outline-none py-2 block w-full bg-transparent border-b-2 border-gray-200"
                placeholder="Search">
        </div>
        <div class="flex-1 h-full overflow-auto px-2">
            <div v-for="room in rooms"
                class="entry cursor-pointer transform hover:scale-105 duration-300 transition-transform bg-white mb-4 rounded p-4 flex shadow-md"
                @click='getRoom(room)'>
                <div class="flex-2">
                    <div class="w-12 h-12 relative">
                        <img class="w-12 h-12 rounded-full mx-auto" :src="(getRoomUser(room).avatar == 'images/default1.png') ? getRoomUser(room).avatar :'/storage/files/users/'+ getRoomUser(room).user_id + '/' + getRoomUser(room).avatar" alt="chat-user" />
                        <span
                            class="absolute w-4 h-4 rounded-full right-0 bottom-0 border-2 border-white" :class="room.room_id == currentRoom && isActive ? 'bg-green-400': 'bg-gray-400' "></span>
                    </div>
                </div>
                <div class="flex-1 px-2">
                    <div class="truncate w-32"><span class="text-gray-800">{{getRoomUser(room).user_name}}</span>
                    </div>
                    <div><small class="text-gray-600">{{ (room.message) ? room.message.substring(0,40)+".." : '' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message -->
    <Messages :user="user" :currentRoom="currentRoom" @isActive="getActiveUser"/>

</template>

<script>
    export default {
        name: 'Rooms',
        props: ['rooms'],
        data() {
            return {
                user: '',
                currentRoom: 2,
                isActive: false
            }
        },

        methods: {
            getRoomUser($room) {
                return ($room.user[0] != null) ? $room.user[0] : $room.user[1];
            },
            getRoom(room) {
                this.currentRoom = room.room_id;
                this.user = this.getRoomUser(room);
            },
            getActiveUser(isActive) {
                this.isActive = isActive;
            }
        },
    }

</script>
