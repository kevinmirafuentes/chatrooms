<template>
	<div class="row">
		<chatroom class="col-md-4"
			v-for="window in windows"
			:key="window.id"
			:id="window.id"
			:name="window.name"
			></chatroom>
	</div>
</template>

<script>
	import Bus from '../../bus'

	export default {
		data () {
			return {
				windows: []
			}
		},
		methods: {

		},
		mounted () {
			Bus.$on('chatroom.opened', chatroom => {
				this.windows.push(chatroom)
			})

			// test
			Bus.$emit('chatroom.opened', {id:1, name: 'Chatroom #1'});
			Bus.$emit('chatroom.opened', {id:2, name: 'Chatroom #2'});
			Bus.$emit('chatroom.opened', {id:3, name: 'Chatroom #3'});
			Bus.$emit('chatroom.opened', {id:4, name: 'Chatroom #4'});
		}
	}
</script>

<style lang="scss">

</style>