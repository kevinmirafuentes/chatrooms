<template>
	<div class="chatroom row">
		<div v-if="!error && chatroom != null" >
			<div class="col-md-8">
				<h4>{{ chatroom.name }}</h4>
				<div class="chatroom__messages" ref="messages">
					<chatroom-message v-for="message in messages.slice().reverse()" :key="message.id" :message="message"></chatroom-message>
				</div>
				<form v-if="permission !== 1">
					<textarea
						id="body"
						class="chatroom__form-input"
						cols="30"
						rows="4"
						v-model="body"
						@keydown="handleMessageInput"
					></textarea>
					<span class="chatroom__form-helptext">
						Hit return to send or Ctrl + Return for a new line
					</span>
				</form>

				<span v-if="permission === 1">You are currently on Readonly permission.</span>
			</div>
			<div class="col-md-4">
				<h3>Members</h3>
				<div class="pull-right">
					<a href="#"><small>Readonly all..</small></a>
				</div>
				<div class="clearfix"></div>
				<chatroom-user v-for="member in members" :key="member.id" :member="member"></chatroom-user>
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
	import moment from 'moment'

	export default {
		data () {
			return {
				chatroom: null,
				messages: [],
				nextPageUrl: null,
				error: null,
				members: [],
				body: '',
				permission: 0
			}
		},
		methods: {
			loadMessages (chatroomId, event) {
				if (event) {
					event.preventDefault()
				}

				axios.get('/chat/chatrooms/'+chatroomId+'/messages')
				.then((response) => {
					Bus.$emit('chatroom.messages.loaded', response.data)
				})
				.catch(() => {
					this.error = 'Failed to load messages.'
				})
			},
			handleMessageInput (e) {
				if (e.keyCode === 13 && !e.shiftKey) {
					e.preventDefault()
					this.send()
				}
			},
			buildTempMessage () {
				let tempId = Date.now();

				return {
					id: tempId,
					body: this.body,
					self_owned: true,
					sending: true,
					failed: false,
					created_at: moment().utc(0).format('YYYY-MM-DD HH:mm:ss'),
					user: {
						name: Backend.user.name
					}
				}
			},
			send () {
				if (!this.body || this.body.trim === '') {
					return
				}

				let tempMessage = this.buildTempMessage()
				Bus.$emit('message.added', tempMessage)
			},
			scrollToLatest () {
				this.$nextTick(function () {
					this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight
				})
			},
			sendMessagePool () {
				for (var i = this.messages.length; i > 0; i--) {
					let message = this.messages[i-1]

					if (message.failed || !message.sending) {
						continue
					}

					axios.post('/chat/chatrooms/'+this.chatroom.id+'/messages', {
						body: message.body
					})
					.then((response) => {
						Bus.$emit('message.saved', message)
						this.sendMessagePool()
					})
					.catch(() => {
						Bus.$emit('message.failed', message)
						this.sendMessagePool()
					})
				}
			}
		},
		mounted () {
			Bus.$on('chatroom.selected', (chatroom) => {
				this.chatroom = chatroom
				this.permission = chatroom.pivot.permission
				this.loadMessages(chatroom.id)
				console.log('chat room selected')
				Bus.$emit('chatroom.entered', chatroom.id)
			})
			.$on('chatroom.messages.loaded', (data) => {
				this.messages = data.messages
				this.members = data.members
				this.nextPageUrl = data.next_page_url
				this.error = null
				this.scrollToLatest()
			})
			.$on('message.added', (message) => {
				this.messages.unshift(message)
				this.body = null

				this.scrollToLatest()
				this.sendMessagePool()
			})
			.$on('message.saved', (message) => {
				for (var i = 0; i < this.messages.length; i++) {
					if (this.messages[i].id === message.id) {
						this.messages[i].failed = false
						this.messages[i].sending = false
					}
				}
			})
			.$on('message.failed', (message) => {
				for (var i = 0; i < this.messages.length; i++) {
					if (this.messages[i].id === message.id) {
						this.messages[i].failed = true
						this.messages[i].sending = false
						this.scrollToLatest()
					}
				}
			})

			// when message is resending, remove from list
			// and set as the latest message
			// message added is then triggered again to
			// attach the message and send to server
			Bus.$on('message.resending', (message) => {
				console.log('message is removed', message)
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
			border: 1px solid #d3e0e9;
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