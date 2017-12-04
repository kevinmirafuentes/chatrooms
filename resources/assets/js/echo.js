import Bus from './bus'

window.online = []

// checks for logged in users
Echo.join('presence')
	.here((users) => {
		users.forEach((u) => {
			window.online.push(u.id);
		})
		Bus.$emit('user.here', users)
	})
	.joining((user) => {
		if (window.online.length < 1 || !window.online.includes(user.id)) {
			window.online.push(user.id)
		}
		Bus.$emit('user.joined', user)
		console.log('joined', user)
	})
	.leaving((user) => {
		let idx = window.online.indexOf(user.id)
		if (idx > -1) {
			window.online.splice(idx, 1)
		}
		Bus.$emit('user.left', user)
		console.log('left', user)
	})

if (Backend.user.id) {
	Echo.private('App.Models.User.' + Backend.user.id)
		.notification((notification) => {
			var event = notification.type.replace(/\\/g, '.')
			Bus.$emit(event, notification)
		})
}

Bus.$on('App.Notifications.Chat.ChatroomCreated', (e) => {
	Bus.$emit('chatroom.created', e.chatroom)
});
Bus.$on('App.Notifications.Chat.MessageCreated', (e) => {
	Bus.$emit('message.added', e.message)
});
Bus.$on('App.Notifications.Chat.UserPermissionChanged', (e) => {
	Bus.$emit('user-permission.changed', e.data);
});
