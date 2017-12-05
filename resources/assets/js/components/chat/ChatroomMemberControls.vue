<template>
	<div>
		<button type="button" class="btn btn-primary btn-sm pull-left"  @click="showModal">Add Members</button>
		<a href="#" class="pull-right" @click="changeUsersToReadonly"><small>Readonly all..</small></a>

		<div class="modal fade add-member-modal" id="add-member-modal" tabindex="-1" role="dialog" aria-labelledby="add-member-modal">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Add New Members</h4>
					</div>
					<div class="modal-body">
						<form>
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
										<li class="new-chatroom-modal__user" v-for="user in availableUsers" @click="selectUser(user.id)" v-if="!user.sel">
											{{ user.name }}
										</li>
									</ul>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="new-chatroom-modal__submit btn btn-primary" @click="addMembers" data-loading-text="Loading...">Add</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Bus from '../../bus'

	export default {
		props: ['chatroom'],
		data () {
			return {
				selectedUsers: [],
				availableUsers: [],
				modal: null
			}
		},
		methods: {
			unselectUser (id) {
				this.selectedUsers = this.selectedUsers.filter((user) => {
					let check = user.id !== id

					this.availableUsers.forEach((u, k) => {
						if (u.id === user.id) {
							this.availableUsers[k].sel = false
						}
					})

					return check
				})
			},
			selectUser (id) {
				this.availableUsers.forEach((user, key) => {
					if (id === user.id) {
						this.selectedUsers.push(user)
						this.availableUsers[key].sel = true
					}
				})
			},
			addMembers () {
				let memberIds = []

				this.selectedUsers.forEach((user) => {
					memberIds.push(user.id)
				})

				let btn = $('.new-chatroom-modal__submit');
				btn.button('loading')

				axios.post('chat/chatrooms/'+this.chatroom.id+'/members', {
					members: memberIds
				})
				.then((response) => {
					Bus.$emit('chatroom.members.added', response.data)
					this.selectedUsers = []
					this.modal.modal('hide')
					btn.button('reset')
				})
				.catch(() => {
					btn.button('reset')
					alert('Failed to add members. Please try again')
				})

			},
			changeUsersToReadonly (e) {
				Bus.$emit('users.to.readonly', e)
			},
			loadAvailableUsers () {
				axios.get('/chat/chatrooms/'+this.chatroom.id+'/available-users').then((response) => {
					this.availableUsers = response.data
					this.selectedUsers = []
					this.availableUsers.forEach((user, key) => {
						this.availableUsers[key].sel = false
					})
				})
			},
			showModal () {
				this.modal.modal('show')
			}
		},
		mounted () {
			this.modal = $('#add-member-modal');

			this.modal.on('shown.bs.modal', () => {
				this.loadAvailableUsers()
			})
		}
	}
</script>