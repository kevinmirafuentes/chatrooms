<template>
	<div class="chatrooms row">
		<h3>Chat Rooms</h3>
		<div class="chatrooms__item" v-for="chatroom in chatrooms">
			<a href="#"
				@click="selectChatroom(chatroom, $event)"
				:class="{ 'badge chatrooms__link--selected': selectedChatroom == chatroom.id }"
			>{{ chatroom.name }}</a>
		</div>
	</div>
</template>

<script>
	import Bus from '../../bus'

	export default {
		data () {
			return {
				chatrooms: [],
				selectedChatroom: null
			}
		},
		mounted () {
			axios.get('/chat/chatrooms').then((response) => {
				this.chatrooms = response.data
			})

			Bus.$on('chatroom.selected', (chatroom) => {
				this.selectedChatroom = chatroom.id;
			})
		},
		methods: {
			selectChatroom: (chatroom, event) => {
				event.preventDefault()
				Bus.$emit('chatroom.selected', chatroom)
			}
		}
	}
</script>

<style lang="scss">
	.chatrooms {
		border-radius: 3px;
		border: 1px solid #d3e0e9;
		background-color: #fff;
		padding: 10px;
		margin: 0;

		&__link--selected {
			background: #3097D1;
		}
	}
</style>