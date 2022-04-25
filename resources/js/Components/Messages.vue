<template>
    <div class="chat-area flex-1 flex flex-col">
        <div class="flex-3">
            <h2 class="text-xl py-1 mb-8 border-b-2 border-gray-200">
                Chatting with <b>{{ user.user_name }}</b></h2>
        </div>
        <div class="messages flex-1 overflow-auto">
            <div v-for='(msg, index) in messages' class="message mb-4 flex"
                :class="checkUser(msg.user) ? ' me text-right ' : ''" :key="index">
                <div class="flex-2" v-if='!checkUser(msg.user)'>
                    <div class="w-12 h-12 relative">
                        <img class="w-12 h-12 rounded-full mx-auto" src="/images/profile-image.png" alt="chat-user" />
                        <span class="absolute w-4 h-4 rounded-full right-0 bottom-0 border-2 border-white"
                            :class="activeUser ? 'bg-green-400' : 'bg-gray-400' "></span>
                    </div>
                </div>
                <div class="flex-1 px-2">
                    <div class="inline-block rounded-full p-2 px-6 "
                        :class="checkUser(msg.user) ? ' bg-blue-600 text-white ' :' bg-gray-300 text-gray-700'">
                        <span>{{msg.message}}</span>
                    </div>
                    <div class="pl-4"><small class="text-gray-500">{{msg.time}}</small></div>
                </div>
            </div>
            <div class="message mb-4 flex" v-if='typing'>
                <div class="flex-2">
                    <div class="w-12 h-12 relative">
                    </div>
                </div>
                <div class="flex-1 px-2">
                    <div class="inline-block rounded-full p-2 px-6 bg-gray-300 text-gray-700">
                        <b>{{user.user_name}} is typing.....</b>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-2 pt-4 pb-10">
            <div class="write bg-white shadow flex rounded-lg">
                <div class="flex-3 flex content-center items-center text-center p-4 pr-0">
                    <span class="block text-center text-gray-400 hover:text-gray-800">
                        <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            stroke="currentColor" viewBox="0 0 24 24" class="h-6 w-6">
                            <path
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="flex-1">
                    <textarea name="message" class="w-full block outline-none py-4 px-4 bg-transparent" rows="1"
                        placeholder="Type a message..." autofocus @keyup.enter='sendMessage()'
                        @keydown='sendTypingEvent()' v-model='this.message'></textarea>
                </div>
                <div class="flex-2 w-32 p-2 flex content-center items-center">
                    <div class="flex-1 text-center">
                        <span class="text-gray-400 hover:text-gray-800">
                            <span class="inline-block align-text-bottom">
                                <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                                    <path
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                    </path>
                                </svg>
                            </span>
                        </span>
                    </div>
                    <div class="flex-1">
                        <button class="bg-blue-400 w-10 h-10 rounded-full inline-block">
                            <span class="inline-block align-text-bottom">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4 text-white">
                                    <path d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props: ['user', 'currentRoom', 'isActive'],
        data() {
            return {
                users: [],
                messages: [],
                message: '',
                typing: false,
                room_id: '',
                activeUser: false
            }
        },
        watch: {
            currentRoom: {
                handler(value) {
                    this.updateRoom();
                    this.fetchMessages();
                },
                immediate: true // This ensures the watcher is triggered upon creation
            }

        },
        computed: {
            getUser() {
                return this.$page.props.auth.user;
            }
        },
        methods: {
            checkUser($user_id) {
                return this.getUser.user_id === $user_id;
            },
            listenTypingEvent() {
                this.typing = true;
                setTimeout(() => {
                    this.typing = false;
                }, 1500);
            },
            sendMessage() {
                if (this.message.length > 0) {
                    // send message to server
                    axios.post(route('send_message', this.currentRoom), {
                        message: this.message,
                        channel_id: this.currentRoom
                    }).then(response => {
                        this.messages.push({
                            message: this.message,
                            user: this.getUser.user_id,
                            time: new Date().toLocaleTimeString(),
                        });
                        this.message = '';
                    });

                }
            },
            sendTypingEvent() {
                Echo.join(`chat.${this.currentRoom}`)
                    .whisper('typing', this.user);
            },
            updateRoom() {
                Echo.leave(`chat.${this.room_id}`);
                Echo.join(`chat.${this.currentRoom}`)
                    .here(user => {
                        this.activeUser = true;
                        this.$emit('isActive', this.activeUser);
                    })
                    .joining(user => {
                        this.activeUser = true;
                        this.$emit('isActive', this.activeUser);
                    })
                    .leaving(user => {
                        //this.users = this.users.filter(u => u.id != user.id);
                        this.activeUser = false;
                        this.$emit('isActive', this.activeUser);

                    })
                    .listen('MessageEvent', (e) => {
                        this.messages.push({
                            message: e.message,
                            user: this.user,
                            time: e.time,
                        });
                        this.room_id = this.currentRoom;
                    })
                    .listenForWhisper('typing', (e) => {
                        this.listenTypingEvent();
                    });
            },
            fetchMessages() {
                axios.post(route('get_messages', this.currentRoom), {
                    room: this.currentRoom,
                }).then(response => {
                    this.messages = response.data;
                });
            },
            
        },
        created() {
            this.fetchMessages();
            this.room_id = this.currentRoom;
        },
    }

</script>
