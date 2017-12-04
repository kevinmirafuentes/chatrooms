<template>
	<div class="chatroom-member" :class="{ 'chatroom-member--online': isOnline }">
		<a href="#" class="chatroom-member__name">{{ member.name }}</a>
		<a href="#"
			class="pull-right chatroom-member__readonly badge"
			:class="{ 'chatroom-member__readonly--active': member.permission === 1 }"
			@click="toggleReadonlyPermission(member)"
			v-if="isOwner"
		><small>Readonly</small></a>
	</div>
</template>

<script>
	import Bus from '../../bus'

	export default {
		props: ['member', 'permission', 'isOwner'],
		data () {
			return {
				isOnline: false
			};
		},
		methods: {
			toggleReadonlyPermission (user) {
				Bus.$emit('member.toggle-readonly', user.id)
			},
			updateStatus () {
				if (window.online.includes(this.member.id)) {
					this.isOnline = true
				} else {
					this.isOnline = false
				}
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

<style lang="scss">
	.chatroom-member {
		position: relative;

		&--online {
			&:before {
				content: "";
				display: block;
				position: absolute;
				width: 10px;
				height: 10px;
				left: -15px;
				top: 5px;
				background: #0fd40f;
				border-radius: 50%;
				overflow: hidden;
			};
		}

		&__readonly {
			opacity: 0.5;
		}

		&__readonly--active {
			background: #3097D1;
			opacity: 1;
		}

		&__name {
			width: 60%;
			display: inline-block;
			text-overflow: ellipsis;
			white-space: nowrap;
			overflow: hidden;
		}
	}
</style>