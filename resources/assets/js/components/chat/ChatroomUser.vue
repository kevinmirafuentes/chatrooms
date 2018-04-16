<template>
	<div class="chatroom-member" :class="{ 'chatroom-member--online': isOnline }">
		<a href="#" class="chatroom-member__name">{{ member.name }}</a>
		<div class="pull-right" v-if="isOwner">
			<a href="#" class="chatroom-member__remove" @click="removeUser(member.id, $event)">
				<i class="glyphicon glyphicon-remove text-danger"></i>
			</a>
			<a href="#"
				class="chatroom-member__readonly badge"
				:class="{ 'chatroom-member__readonly--active': member.permission === 1 }"
				@click="toggleReadonlyPermission(member, $event)">
				<small>Readonly</small>
			</a>
		</div>
	</div>
</template>

<script>
	import Bus from '../../bus'

	export default {
		props: [
			'member',
			'permission',
			'isOwner',
			'chatroomId'
		],
		data () {
			return {
				isOnline: false
			};
		},
		methods: {
			toggleReadonlyPermission (user, e) {
				e.preventDefault()
				// Bus.$emit('member.toggle-readonly', user.id)
			},
			updateStatus () {
				if (window.online.includes(this.member.id)) {
					this.isOnline = true
				} else {
					this.isOnline = false
				}
			},
			removeUser (id, e) {
				e.preventDefault()
				Bus.$emit('member.removed', id)

				axios.post('/chat/chatrooms/'+this.chatroomId+'/remove-member', {
					user_id: id
				})
			}
		},
		mounted () {
			this.updateStatus()
			Bus.$on('user.joined', (user) => {
				this.updateStatus()
			})
			.$on('user.left', (user) => {
				this.updateStatus()
			})
		}
	}
</script>
