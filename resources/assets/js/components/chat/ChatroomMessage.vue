<template>
	<div class="chatroom-message" :class="{
		'chatroom-message--own': message.self_owned,
		'chatroom-message--is-sending': message.sending
	}">
		<div class="chatroom-message__head">
			<span>{{ message.user.name }}</span>
		</div>
		<div class="chatroom-message__body">{{ message.body }}</div>
		<div class="chatroom-message__error text-right" v-if="typeof message.failed != 'undefined' && message.failed">
			<small>Failed</small>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['message']
	}
</script>

<style lang="scss">
	.chatroom-message {
		border-bottom: 1px solid #d3e0e9;
		color: #000;
		padding: 10px;

		&--own {
			background: #d6eefb;
		}

		&--is-sending {
			opacity: 0.5;
		}

		&__error {
			color: #dc3545;
		}

		&__head {
			font-weight: bold;
		}

		&__body {
			min-height: 50px;
			white-space: pre-wrap;
		}

		&:last-child {
			border-bottom: none;
		}
	}
</style>