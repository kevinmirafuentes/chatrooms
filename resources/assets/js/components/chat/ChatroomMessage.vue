<template>

	<div class="message"
		:class="{
			'own': message.self_owned,
			'sending': message.sending
		}">
		<div class="message__header">
			{{ message.user.name }}
			<div class="message__date">{{ formatedDate }}</div>
		</div>
		<div class="message__body">{{ message.body }}</div>
		<div class="message__error text-right" v-if="typeof message.failed != 'undefined' && message.failed">
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
