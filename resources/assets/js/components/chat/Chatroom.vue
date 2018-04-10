<template>
	<div class="col-sm-12">
		<div class="chatroom">
			<div class="chatroom__header">
				<span class="name">{{ name + name + name }}</span>
				<a href="#" class="chatroom__close">
					<i class="glyphicon glyphicon-remove"></i>
				</a>
			</div>
			<div class="chatroom__body"></div>
			<div class="chatroom__footer">
				<textarea class="form-control"></textarea>
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
				// chatroom: null,
				// messages: [],
				// nextPageUrl: null,
				// error: null,
				// members: [],
				// body: '',
				// permission: 0
			}
		},
		methods: {
			// loadMessages (chatroomId, event) {
			// 	if (event) {
			// 		event.preventDefault()
			// 	}

			// 	axios.get('/chat/chatrooms/'+chatroomId+'/messages')
			// 	.then((response) => {
			// 		Bus.$emit('chatroom.messages.loaded', response.data)
			// 	})
			// 	.catch(() => {
			// 		this.error = 'Failed to load messages.'
			// 	})
			// },
			// handleMessageInput (e) {
			// 	if (e.keyCode === 13 && !e.shiftKey) {
			// 		e.preventDefault()
			// 		this.send()
			// 	}
			// },
			// buildTempMessage () {
			// 	let tempId = Date.now();

			// 	return {
			// 		id: tempId,
			// 		chatroom_id: this.chatroom.id,
			// 		body: this.body,
			// 		self_owned: true,
			// 		sending: true,
			// 		failed: false,
			// 		read: true,
			// 		created_at: moment().utc(0).format('YYYY-MM-DD HH:mm:ss'),
			// 		user: {
			// 			name: Backend.user.name
			// 		}
			// 	}
			// },
			// send () {
			// 	if (!this.body || this.body.trim === '') {
			// 		return
			// 	}

			// 	let tempMessage = this.buildTempMessage()
			// 	Bus.$emit('message.added', tempMessage)
			// },
			// scrollToLatest () {
			// 	this.$nextTick(function () {
			// 		if (this.chatroom) {
			// 			this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight
			// 		}
			// 	})
			// },
			// sendMessagePool () {
			// 	for (var i = this.messages.length; i > 0; i--) {
			// 		let message = this.messages[i-1]

			// 		if (message.failed || !message.sending) {
			// 			continue
			// 		}

			// 		axios.post('/chat/chatrooms/'+this.chatroom.id+'/messages', {
			// 			body: message.body
			// 		})
			// 		.then((response) => {
			// 			Bus.$emit('message.saved', message)
			// 			this.sendMessagePool()
			// 		})
			// 		.catch(() => {
			// 			Bus.$emit('message.failed', message)
			// 			this.sendMessagePool()
			// 		})
			// 	}
			// },
			// changeUsersToReadonly (e) {
			// 	e.preventDefault()

			// 	let users = [];

			// 	this.members.forEach((user) => {
			// 		users.push(user.id)
			// 	})

			// 	let params = {
			// 		users: users,
			// 		chatroom: this.chatroom.id
			// 	};

			// 	axios.post('/chat/users/to-readonly', params)
			// 	.then((response) => {
			// 		for (var i=0; i<this.members.length; i++) {
			// 			this.members[i].permission = 1;
			// 		}
			// 	})
			// 	.catch(() => {})
			// },
			// ping () {
			// 	axios.get('/chat/chatrooms/'+this.chatroom.id+'/ping')
			// }
		},
		mounted () {
			console.log('id: ' + this.id)
			// Bus.$on('chatroom.selected', (chatroom) => {
			// 	this.chatroom = chatroom
			// 	this.isOwner = chatroom.user_id == Backend.user.id
			// 	this.loadMessages(chatroom.id)
			// 	Bus.$emit('chatroom.entered', chatroom.id)

			// 	Bus.$emit('chatroom.unread.changed', {
			// 		chatroom: chatroom.id,
			// 		unread_messages: 0
			// 	})
			// })
			// .$on('chatroom.messages.loaded', (data) => {
			// 	this.messages = data.messages
			// 	this.members = data.members
			// 	this.permission = data.permission
			// 	this.nextPageUrl = data.next_page_url
			// 	this.error = null
			// 	this.scrollToLatest()

			// 	this.messages.forEach((message, key) => {
			// 		this.messages[key].read = true
			// 	})
			// })
			// .$on('message.added', (message) => {
			// 	if (!this.chatroom || message.chatroom_id !== this.chatroom.id) {
			// 		return
			// 	}

			// 	if (!message.read) {
			// 		this.ping()
			// 		Bus.$emit('chatroom.unread.changed', {
			// 			chatroom: this.chatroom.id,
			// 			unread_messages: 0
			// 		})
			// 	}

			// 	message.read = true
			// 	this.messages.unshift(message)
			// 	this.body = null

			// 	this.scrollToLatest()
			// 	this.sendMessagePool()
			// })
			// .$on('message.saved', (message) => {
			// 	for (var i = 0; i < this.messages.length; i++) {
			// 		if (this.messages[i].id === message.id) {
			// 			this.messages[i].failed = false
			// 			this.messages[i].sending = false
			// 		}
			// 	}
			// })
			// .$on('message.failed', (message) => {
			// 	for (var i = 0; i < this.messages.length; i++) {
			// 		if (this.messages[i].id === message.id) {
			// 			this.messages[i].failed = true
			// 			this.messages[i].sending = false
			// 			this.scrollToLatest()
			// 		}
			// 	}
			// })
			// .$on('member.toggle-readonly', (id) => {
			// 	this.members.forEach((user, key) => {
			// 		if (user.id === id) {
			// 			let permission = this.members[key].permission;
			// 			this.members[key].permission = permission === 1 ? 0 : 1;

			// 			let params = {
			// 				users: [id],
			// 				chatroom: this.chatroom.id
			// 			};

			// 			let target = permission === 1 ? '/chat/users/to-collab' : '/chat/users/to-readonly';
			// 			axios.post(target, params)
			// 				.catch(() => {
			// 					// rollback
			// 					this.members[key].permission = permission
			// 				})
			// 		}
			// 	})
			// })
			// .$on('user-permission.changed', (e) => {
			// 	if (e.chatroom == this.chatroom.id) {
			// 		this.permission = e.permission;
			// 	}
			// })
			// .$on('users.to.readonly', (e) => {
			// 	this.changeUsersToReadonly(e)
			// })
			// .$on('chatroom.members.added', (members) => {
			// 	this.members = this.members.concat(members)
			// })
			// .$on('chatroom.members.added.notification', (data) => {
			// 	if (data.chatroom.id) {
			// 		this.members = this.members.concat(data.members)
			// 	}
			// })
			// .$on('member.removed', (id) => {
			// 	this.members = this.members.filter((member) => {
			// 		return member.id != id
			// 	})
			// })

			// // when message is resending, remove from list
			// // and set as the latest message
			// // message added is then triggered again to
			// // attach the message and send to server
			// Bus.$on('message.resending', (message) => {
			// 	//console.log('message is removed', message)
			// })
		}
	}
</script>

<style lang="scss">
	.chatroom {
		border: 1px solid #d3e0e9;
		background-color: #fff;
		margin-bottom: 20px;
		&__header {
			background: #3097D1;
			padding: 10px;
			color: #fff;
			span.name {
				width: 50%;
				text-overflow: ellipsis;
				overflow: hidden;
				display: inline-block;
				white-space: nowrap;
			}
		}
		&__body {
			height: 200px;
			overflow-y: auto;
			padding: 10px;
		}
		&__footer {
			padding: 10px;
			textarea {
				width: 100%;
			}
		}
		&__close {

		}
	}
</style>