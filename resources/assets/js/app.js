
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('chatrooms', require('./components/chat/Chatrooms.vue'))
Vue.component('chatroom', require('./components/chat/Chatroom.vue'))
Vue.component('chatroom-instances', require('./components/chat/ChatroomInstances.vue'))
Vue.component('chatroom-message', require('./components/chat/ChatroomMessage.vue'))
Vue.component('chatroom-user', require('./components/chat/ChatroomUser.vue'))
Vue.component('create-chatroom-modal', require('./components/chat/CreateChatroomModal.vue'))
Vue.component('chatroom-member-controls', require('./components/chat/ChatroomMemberControls.vue'))

const app = new Vue({
    el: '#app'
});
