<template>
	<div class="chatroom-message" :class="{
		'chatroom-message--own': message.self_owned,
		'chatroom-message--is-sending': message.sending
	}">
		<div class="chatroom-message__head">
			<div>{{ message.user.name }}</div>
			<div class="chatroom-message__date">{{ formatedDate }}</div>
		</div>
		<div class="chatroom-message__body">{{ message.body }}</div>
		<div class="chatroom-message__error text-right" v-if="typeof message.failed != 'undefined' && message.failed">
			<small>Failed</small>
		</div>
	</div>
</template>

<script>
	import moment from 'moment'

	export default {
		props: ['message'],
		data () {
			return {
				formatedDate: null
			}
		},
		methods: {
			setAutoupdateDate () {
				let date = this.message.created_at
				let testDate = moment.utc(date, 'YYYY-MM-DD HH:mm:ss')
				this.formatedDate = testDate.local().fromNow()

				setTimeout(() => {
					this.setAutoupdateDate()
				}, 60000)
			}
		},
		mounted () {
			this.setAutoupdateDate()
		}
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

		&__date {
			font-size: 10px;
			font-weight: normal;
		}
	}
</style>