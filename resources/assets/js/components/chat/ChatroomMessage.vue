<template>
	<div class="chatroom-message" :class="{ 'chatroom-message--own': message.self_owned }">
		<div class="chatroom-message__head">
			<span>{{ message.user.name }}</span>
		</div>
		<div class="chatroom-message__body">{{ message.body }}</div>
	</div>
</template>

<script>
	export default {
		props: ['message']
	}
</script>

<style lang="scss">
	.chatroom-message {
		border: 1px solid #d3e0e9;
		margin-bottom: -1px;
		color: #000;
		padding: 10px;

		&--own {
			background: #d6eefb;
		}

		&__head {
			font-weight: bold;
		}

		&__body {
			min-height: 50px;
			white-space: pre-wrap;
		}
	}
</style>