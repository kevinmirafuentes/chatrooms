<template>
	<div class="col-sm-12">
		<div class="chatroom">
			<div class="chatroom__header">
				<span class="name">{{ name }}</span>

				<div class="pull-right">
					<a href=""
						class="chatroom__members dropdown-toggle"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false"
						@click="toggleMembersList()">
						<i class="glyphicon glyphicon-user"></i>
					</a>

					<a href="#"
						class="chatroom__close "
						v-on:click="$emit('close')">
						<i class="glyphicon glyphicon-remove"></i>
					</a>
				</div>
			</div>
			<div style="position: relative; overflow-y: auto;">
				<div class="chatroom__body" :ref="'messages' + id">
					<chatroom-message v-for="message in messages.slice().reverse()" :key="message.id" :message="message"></chatroom-message>
				</div>
				<div class="chatroom__footer">
					<textarea
						class="form-control" v-model="body" @keydown="handleMessageInput"></textarea>
					<span class="chatroom__helptext">
						Hit return to send or Shift + Return for a new line
					</span>
				</div>
				<div class="chatroom__members_list" v-show="showMembers">
					<h4>Members</h4>
					<chatroom-user
						v-for="member in members"
						:key="member.id"
						:permission="permission"
						:member="member"
						:chatroomId="id"></chatroom-user>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Bus from '../../bus'
	import moment from 'moment'

	export default {
		props: ['id', 'name'],
		data () {
			return {
				messages: [],
				nextPageUrl: null,
				error: null,
				members: [],
				body: '',
				permission: 0,
				showMembers: false,
			}
		},
		methods: {
			toggleMembersList() {
				this.showMembers = !this.showMembers;
			},
			loadMessages (chatroomId, event) {
				if (event) {
					event.preventDefault()
				}

				axios.get('/chat/chatrooms/'+chatroomId+'/messages')
				.then((response) => {
					var data = response.data;

					this.messages = data.messages
					this.members = data.members
					this.permission = data.permission
					this.nextPageUrl = data.next_page_url
					this.error = null
					this.scrollToLatest()

					this.messages.forEach((message, key) => {
						this.messages[key].read = true
					})
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
					chatroom_id: this.id,
					body: this.body,
					self_owned: true,
					sending: true,
					failed: false,
					read: true,
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
				Bus.$emit('message.added.'+this.id, tempMessage)
			},
			scrollToLatest () {
				this.$nextTick(function () {
					this.$refs['messages' + this.id].scrollTop = this.$refs['messages' + this.id].scrollHeight
				})
			},
			sendMessagePool () {
				for (var i = this.messages.length; i > 0; i--) {
					let message = this.messages[i-1]

					if (message.failed || !message.sending) {
						continue
					}

					axios.post('/chat/chatrooms/'+this.id+'/messages', {
						body: message.body
					})
					.then((response) => {
						Bus.$emit('message.saved.'+this.id, message)
						this.sendMessagePool()
					})
					.catch(() => {
						Bus.$emit('message.failed.'+this.id, message)
						this.sendMessagePool()
					})
				}
			},
			changeUsersToReadonly (e) {
				e.preventDefault()

				let users = [];

				this.members.forEach((user) => {
					users.push(user.id)
				})

				let params = {
					users: users,
					chatroom: this.id
				};

				axios.post('/chat/users/to-readonly', params)
				.then((response) => {
					for (var i=0; i<this.members.length; i++) {
						this.members[i].permission = 1;
					}
				})
				.catch(() => {})
			},
			ping () {
				axios.get('/chat/chatrooms/'+this.id+'/ping')
			}
		},
		mounted () {
			this.loadMessages(this.id)

			Bus.$on('message.added.'+this.id, (message) => {
				if (!message.read) {
					this.ping()
					Bus.$emit('chatroom.unread.changed', {
						chatroom: this.id,
						unread_messages: 0
					})
				}

				message.read = true
				this.messages.unshift(message)
				this.body = null

				this.scrollToLatest()
				this.sendMessagePool()
			})

			Bus.$on('message.saved.'+this.id, (message) => {
				for (var i = 0; i < this.messages.length; i++) {
					if (this.messages[i].id === message.id) {
						this.messages[i].failed = false
						this.messages[i].sending = false
					}
				}
			})


			Bus.$on('message.failed.'+this.id, (message) => {
				for (var i = 0; i < this.messages.length; i++) {
					if (this.messages[i].id === message.id) {
						this.messages[i].failed = true
						this.messages[i].sending = false
						this.scrollToLatest()
					}
				}
			})


			Bus.$on('member.toggle-readonly.'+this.id, (id) => {
				this.members.forEach((user, key) => {
					if (user.id === id) {
						let permission = this.members[key].permission;
						this.members[key].permission = permission === 1 ? 0 : 1;

						let params = {
							users: [id],
							chatroom: this.chatroom.id
						};

						let target = permission === 1 ? '/chat/users/to-collab' : '/chat/users/to-readonly';
						axios.post(target, params)
							.catch(() => {
								// rollback
								this.members[key].permission = permission
							})
					}
				})
			})

			Bus.$on('user-permission.changed.'+this.id, (e) => {
				this.permission = e.permission;
			})

			Bus.$on('users.to.readonly.'+this.id, (e) => {
				this.changeUsersToReadonly(e)
			})

			Bus.$on('chatroom.members.added.'+this.id, (members) => {
				this.members = this.members.concat(members)
			})


			Bus.$on('chatroom.members.added.notification.'+this.id, (data) => {
				this.members = this.members.concat(data.members)
			})

			Bus.$on('member.removed.'+this.id, (id) => {
				this.members = this.members.filter((member) => {
					return member.id != id
				})
			})
		}
	}
</script>
