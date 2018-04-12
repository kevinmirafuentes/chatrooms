<template>
	<div class="row">
		<chatroom class="col-md-4"
			v-for="(window, index) in windows"
			:key="window.id"
			:id="window.id"
			:name="window.name"
			v-on:close="closeWindow(index)"
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
			closeWindow(index) {
				var id = this.windows[index].id
				this.windows.splice(index, 1)

				Bus.$emit('chatroom.closed', id);
			}
		},
		mounted () {
			Bus.$on('chatroom.opened', chatroom => {
				this.windows.push(chatroom)
			})
		}
	}
</script>

<style lang="scss">

</style>