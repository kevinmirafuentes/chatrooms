import Bus from './bus'

let currentChatroom = null;


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
