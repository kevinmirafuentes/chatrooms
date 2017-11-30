<template>
	<div class="chatroom row">
		<div v-if="!error && chatroom != null" >
			<div class="col-md-8">
				<h4>{{ chatroom.name }}</h4>
				<div class="chatroom__messages" ref="messages">
					<chatroom-message v-for="message in messages.slice().reverse()" :key="message.id" :message="message"></chatroom-message>
				</div>
				<form>
					<textarea
						id="body"
						class="chatroom__form-input"
						cols="30"
						rows="4"
					></textarea>
					<span class="chatroom__form-helptext">
						Hit return to send or Ctrl + Return for a new line
					</span>
				</form>
			</div>
			<div class="col-md-4">
				<h3>Members</h3>
				<div class="pull-right">
					<a href="#"><small>Readonly all..</small></a>
				</div>
				<div class="clearfix"></div>
				<chatroom-user></chatroom-user>
				<chatroom-user></chatroom-user>
				<chatroom-user></chatroom-user>
			</div>
		</div>
		<div v-if="!error && chatroom == null" class="chatroom__placeholder">
			Please select your chatroom
		</div>
		<div v-if="error && chatroom != null" class="chatroom__placeholder">
			{{ error }} <a @click="loadMessages(chatroom.id, $event)">Try again?</a>
		</div>
	</div>
</template>

<script>
	import Bus from '../../bus'

	export default {
		data () {
			return {
				chatroom: null,
				messages: [],
				nextPageUrl: null,
				error: null
			}
		},
		methods: {
			loadMessages (chatroomId, event) {
				if (event) {
					event.preventDefault();
				}

				axios.get('/chat/chatrooms/'+chatroomId+'/messages')
				.then((response) => {
					Bus.$emit('chatroom.messages.loaded', response.data);
				})
				.catch(() => {
					this.error = 'Failed to load messages.'
				})
			}
		},
		mounted () {
			Bus.$on('chatroom.selected', (chatroom) => {
				this.chatroom = chatroom
				this.loadMessages(chatroom.id)
			})

			Bus.$on('chatroom.messages.loaded', (data) => {
				this.messages = data.messages
				this.nextPageUrl = data.next_page_url
				this.error = null

				let self = this
				setTimeout(() => {
					self.$refs.messages.scrollTop = self.$refs.messages.scrollHeight
				}, 500)
			})
		}
	}
</script>

<style lang="scss">
	.chatroom {
		border-radius: 3px;
		border: 1px solid #d3e0e9;
		background-color: #fff;
		padding: 10px 0;

		&__messages {
			height: 400px;
			max-height: 400px;
			overflow-y: auto;
			border-bottom: 1px solid #d3e0e9;
			margin-bottom: 20px;
		}

		&__form-input {
			border: 1px solid #d3e0e9;
			width: 100%;
		}

		&__placeholder {
			padding: 20px;
			height: 400px;
			min-height: 400px;
			text-align: center;
		}
	}
</style>