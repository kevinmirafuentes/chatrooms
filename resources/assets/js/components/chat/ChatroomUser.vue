<template>
	<div class="chatroom-member">
		<a href="#" class="chatroom-member__name">{{ member.name }}</a>
		<a href="#" class="pull-right chatroom-member__readonly badge" :class="{ 'chatroom-member__readonly--active': member.permission === 1 }"><small>Readonly</small></a>
	</div>
</template>

<script>
	export default {
		props: ['member']
	}
</script>

<style lang="scss">
	.chatroom-member {
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