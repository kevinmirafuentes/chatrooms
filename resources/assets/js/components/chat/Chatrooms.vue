<template>
	<div class="chatrooms row">
		<h3>Chat Rooms</h3>

		<create-chatroom-modal></create-chatroom-modal>

		<div class="chatrooms__item" v-for="chatroom in chatrooms">
			<a href="#"
				@click="selectChatroom(chatroom, $event)"
				class="chatrooms__link"
				:class="{ 'chatrooms__link--selected': isOpenChatroom(chatroom.id) }"
			>
				{{ chatroom.name }}
				<i class="badge pull-right unread-messages-counter" v-if="chatroom.unread_messages">{{ chatroom.unread_messages }}</i>
			</a>
		</div>
	</div>
</template>

<script>
	import Bus from '../../bus'

	export default {
		data () {
			return {
				chatrooms: [],
				selectedChatroom: null,
				selectedChatrooms: [],
				showCreateChatroomModal: false
			}
		},
		mounted () {
			axios.get('/chat/chatrooms').then((response) => {
				this.chatrooms = response.data
				Bus.$emit('chatrooms.loaded', response.data)
			})

			Bus.$on('chatroom.created', (chatroom) => {
				this.chatrooms.unshift(chatroom)
			})

			Bus.$on('chatroom.unread.changed', (data) => {
				this.chatrooms.forEach((chatroom, key) => {
					if (chatroom.id === data.chatroom) {
						this.chatrooms[key].unread_messages = data.unread_messages
					}
				})
			})

			Bus.$on('chatroom.closed', id => {
				var index = this.selectedChatrooms.indexOf(id)
				this.selectedChatrooms.splice(index, 1);
			})
		},
		methods: {
			isOpenChatroom (id) {
				return this.selectedChatrooms.indexOf(id) != -1
			},
			selectChatroom (chatroom, event) {
				event.preventDefault()
				if (this.isOpenChatroom(chatroom.id)) {
					return
				}

				Bus.$emit('chatroom.opened', chatroom)

				this.selectedChatrooms.push(chatroom.id)
			}
		}
	}
</script>

<style lang="scss">
	.chatrooms {
		padding: 10px;
		margin: 0;
		overflow-y: auto;

		&__link {
			display: block;
			padding: 5px 10px;
			width: 100%;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
			padding-right: 30px;
			position: relative;

			.unread-messages-counter {
				position: absolute;
				top: 5px;
				right: 0;
			}
		}

		&__link--selected {
			background: #eee;
			font-weight: bold;
			color: #3097D1;
			text-decoration: none;
		}
	}
</style>