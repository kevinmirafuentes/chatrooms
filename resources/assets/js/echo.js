import Bus from './bus'

let currentChatroom = null;

Bus.$on('chatroom.entered', (chatroomId) => {
	if (currentChatroom) {
		Echo.leave(currentChatroom)
	}

	currentChatroom = 'chat.'+chatroomId;

	Echo.join(currentChatroom)
		.joining((user) => {
			// console.log('user joined chatroom '+chatroomId, user)
		})
		.leaving((user) => {
			// console.log('user left chatroom '+chatroomId, user)
		})
		.listen('Chat.MessageCreated', (e) => {
			Bus.$emit('message.added', e.message)
		})
		.listen('Chat.UserPermissionChanged', (e) => {
			Bus.$emit('user-permission.changed', e);
		})
})

