<template>
	<div>
		<button type="button" class="btn btn-primary btn-sm new-chatroom-modal-button" data-toggle="modal" data-target="#create-chatroom-modal">
			Create New Chatroom
		</button>

		<div class="modal fade new-chatroom-modal" id="create-chatroom-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Create a new chatroom</h4>
					</div>
					<div class="modal-body">
						<form>
							<div class="form-group">
								<label>Chatroom Name</label>
								<input type="text" class="form-control" v-model="name">
							</div>

							<div class="row">
								<div class="col-md-6">
									<label>Selected Members</label>
									<ul class="new-chatroom-modal__users">
										<li class="new-chatroom-modal__user new-chatroom-modal__selected-users" v-for="user in selectedUsers" @click="unselectUser(user.id)">
											{{ user.name }}
										</li>
									</ul>
								</div>
								<div class="col-md-6">
									<label>Choose Members</label>
									<ul class="new-chatroom-modal__users new-chatroom-modal__users-list">
										<li class="new-chatroom-modal__user" v-for="user in availableUsers" @click="selectUser(user.id)">
											{{ user.name }}
										</li>
									</ul>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="new-chatroom-modal__submit btn btn-primary" @click="send" data-loading-text="Loading...">Create</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Bus from '../../bus'

	export default {
		data () {
			return {
				name: '',
				selectedUsers: [],
				availableUsers: []
			}
		},
		methods: {
			setName () {
				let names = [];

				this.selectedUsers.forEach((user) => {
					names.push(user.name)
				})

				this.name = names.join(',')

				if (this.name.length > 30) {
					this.name = this.name.substr(0, 30)
					this.name += '...'
				}
			},
			unselectUser (id) {
				this.selectedUsers = this.selectedUsers.filter((user) => {
					return user.id !== id
				})
			},
			selectUser (id) {
				for (let i = 0; i <= this.availableUsers.length; i++) {
					let selectedUser = this.availableUsers[i]
					if (id === selectedUser.id) {
						this.selectedUsers.push(selectedUser)
						this.availableUsers = this.availableUsers.filter((user) => {
							return user.id !== id
						})
						break
					}
				}
			},
			send (e) {
				e.preventDefault()

				if (this.selectedUsers.length < 1) {
					return
				}

				if (!this.name || this.name.trim() === '') {
					this.setName()
				}

				let payload = {
					name: this.name,
					users: []
				}

				this.selectedUsers.forEach((user) => {
					payload.users.push(user.id)
				})

				let btn = $('.new-chatroom-modal__submit');

				btn.button('loading')

				axios.post('/chat/chatrooms', payload)
				.then((response) => {
					Bus.$emit('chatroom.created', response.data)
					$('#create-chatroom-modal').modal('hide')
					btn.button('reset')
					this.name = null
				})
				.catch(() => {
					btn.button('reset')
					alert('Faled to create chatroom. Please try again later.')
				})
			}
		},
		mounted () {
			$('#create-chatroom-modal').on('shown.bs.modal', () => {
				axios.get('/chat/users').then((response) => {
					Bus.$emit('create-chatroom.users-loaded', response.data)
				})
			})

			Bus.$on('create-chatroom.users-loaded', (users) => {
				this.availableUsers = users
				this.selectedUsers = []
			})
		}
	}
</script>

<style lang="scss">
	.new-chatroom-modal-button {
		margin: 10px 0;
	}
	.new-chatroom-modal {
		&__users {
			padding: 0;
		}

		&__user {
			padding: 3px 10px;
			list-style: none;
		}

		&__user:hover {
			background: #1f648b;
			color: #fff;
			cursor: pointer;
		}

		&__users-list {
			max-height: 200px;
			overflow-y: auto;
		}

		&__selected-users {
			max-height: 200px;
			overflow-y: auto;
		}
	}
</style>