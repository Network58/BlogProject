<template>
    <div>
        <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Add Role<Button type="dashed" @click="addModal= true"><Icon type="md-add-circle"/>Add Role</Button></p>
					

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>Id</th>
								<th>Role Name</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(role,i) in roles" :key="i" v-if="roles.length">
								<td>{{role.id}}</td>
								<td class="_table_name">{{role.roleName}}</td>
								<td>{{role.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(role, i)">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(role, i)" :loading="role.isDeleting"> Delete</Button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				 <Page :total="100" />

			</div>
		</div>
		<!--role adding model -->
		<Modal v-model="addModal" title="Add Roles"	:mask-closable="false"	:closable="false">
			<Input v-model="data.roleName" placeholder="Enter Role" style="width: 300px"/>
			<div slot="footer">
				<Button type="default" @click="addModal= false">Close</Button>
				<Button type="primary" @click="addRole" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding....':'Add Role'}}</Button>
			</div>				
		</Modal>

			<!--role editing model -->
		<Modal v-model="editModal" title="Edit Role" :mask-closable="false" :closable="false">
			<Input v-model="editData.roleName" placeholder="Edit Role Name" style="width: 300px"/>
			<div slot="footer">
				<Button type="default" @click="editModal= false">Close</Button>
				<Button type="primary" @click="editRole" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing....':'Edit Role'}}</Button>
			</div>				
		</Modal> 

		<!-- delete alert modal -->
		<!-- <Modal v-model="deleteModal" width="360" title="Delete Tag" :mask-closable="false" :closable="false">
			<p slot="header" style="color:#f60;text-align:center">
				<Icon type="ios-information-circle"></Icon>
					<span>Delete confirmation</span>
			</p>
			<div style="text-align:center">
				<p>Are you sure you want to delete tag?.</p>
						
			</div>
			<div slot="footer">
				<Button type="error" size="large"  :loading="isDeleting" :disabled="isDeleting" @click="deleteTag" >Delete</Button>
				<Button type="default" size="large"  @click="deleteModal= false">Close </Button>
			</div>
		</Modal> -->
		<deleteModal/>
    </div>
</template>
<script>
import deleteModal from '../components/deleteModalPage'
import {mapGetters} from 'vuex'
export default {
	data(){
		return{
			data:{
				roleName: '',
				
			},
			roles:[],
			addModal:false,
			editModal: false,
			isAdding: false,
			editData:{
				roleName: ''
			},
			index : -1,
			deleteModal:false,
			deleteItem: {},
			deletingIndex: -1,
			isDeleting: false,

		}
	},

	methods: {
		    async addRole(){
			this.isAdding = true
			if(this.data.roleName.trim()=='') return this.e('Role Name is Required')
			const res = await this.callApi('post', 'app/create_role', this.data)
			if(res.status=== 201){
				this.roles.unshift(res.data)
				this.s('Role was passed Successfully')
				this.addModal = false
				this.data.roleName = ''
				this.isAdding= false
			}else{
				if (res.status===422){
					if(res.data.errors.roleName){
						this.e(res.data.errors.roleName[0])
					}else
				this.swr()
				}
			}
		},
		async editRole(){
			this.isAdding=true
			if(this.editData.roleName.trim()=='') return this.e('Role name is required')
			const res = await this.callApi('post', 'app/edit_role', this.editData)
			if(res.status===200){
				this.roles[this.index].roleName = this.editData.roleName
				this.s('Role has been edited successfully!')
				this.editModal = false
				this.isAdding= false
				
			}else{
				if(res.status==422){
					if(res.data.errors.roleName){
						this.e(res.data.errors.roleName[0])
					}
					
				}else{
					this.swr()
				}
				
			}

		},
		showEditModal(role, index){
			let obj = {
				id: role.id,
				roleName: role.roleName,
			}
			this.editData = obj
			this.editModal = true
			this.index = index
		},
		// async deleteTag(tag, i){
		// 	//(!confirm('Are You Sure You Want To Delete This Tag?')) return
		// 	//this.$set(tag, 'isDeleting', true)
		// 	const res = await this.callApi('post', 'app/delete_tag', this.deleteItem)
		// 	if (res.status===200) {
		// 		this.tags.splice(this.i, 1)
		// 		this.s('Tag has ben successfully deleted')
		// 		this.deleteModal = false
		// 	}else{this.swr()}
		// },
		showDeletingModal(role, i){
			const deleteModalObj = {
				showDeleteModal: true,
				deleteUrl:'app/delete_role',
				data: role,
				deletingIndex: i,
				isDeleted: false
			};
			this.$store.commit("setDeletingModalObj", deleteModalObj);
			// console.log('delete method called')
			// this.deleteItem= category,
			// this.i= deletingIndex,
			// this.deleteModal = true

		},
	},
	async created(){
		const res = await this.callApi('get', 'app/get_roles', this.roles)
		if(res.status === 200){
				this.roles = res.data
			}else{
				this.swr()
			}
	},
	components:{
		deleteModal
	},
	computed: {
    ...mapGetters(["getDeleteModalObj"])
  	},

	watch: {
		getDeleteModalObj(obj) {
			if (obj.isDeleted) {
				this.roles.splice(obj.deletingIndex, 1);
			}
		}
	}
}
</script>