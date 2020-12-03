<template>
    <div>
        <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Admin Users<Button type="dashed" @click="addModal= true"><Icon type="md-add-circle"/>Add Admin User</Button></p>
					

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>Id</th>
								<th>Full Name</th>
                                <th>Email</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(user,i) in users" :key="i" v-if="users.length">
								<td>{{user.id}}</td>
								<td class="_table_name">{{user.fullName}}</td>
                                <td class="">{{user.email}}</td>
								<td>{{user.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(user, i)">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(user, i)" :loading="user.isDeleting"> Delete</Button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				 <Page :total="100" />

			</div>
		</div>
		<!--tag adding model -->
		<Modal v-model="addModal" title="Add Admin User"	:mask-closable="false"	:closable="false">
            <div class="space">
                <Input v-model="data.fullName" type="text" placeholder="Full Name" style="width: 300px"/>
            </div>
            <div class="space">
                <Input v-model="data.email" type="email" placeholder="Email" style="width: 300px"/>
            </div>
            <div class="space">
                <Input v-model="data.password" type="password" placeholder="Password" style="width: 300px"/>
            </div>
			<div class="space">
                <Input v-model="confirmPassword" type="password" placeholder="Confirm Password" style="width: 300px"/>
            </div>
            <div>
                <Select v-model="data.role_id" placeholder="Select Admin Type">
                    <Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{r.roleName}}</Option>
                    <!-- <Option value="Editor">Editor</Option> -->
                </Select>
            </div>
			

			<div slot="footer">
				<Button type="default" @click="addModal= false">Close</Button>
				<Button type="primary" @click="addAdmin" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding....':'Add Admin User'}}</Button>
			</div>				
		</Modal>

			<!--tag editing model -->
		<Modal v-model="editModal" title="Edit Admin User" :mask-closable="false" :closable="false">
			<div class="space">
                <Input v-model="editData.fullName" type="text" placeholder="Full Name" style="width: 300px"/>
            </div>
            <div class="space">	
                <Input v-model="editData.email" type="email" placeholder="Email" style="width: 300px"/>
            </div>
            <div class="space">
                <Input v-model="editData.password" type="password" placeholder="Password" style="width: 300px"/>
            </div>
			<!-- <div class="space">
                <Input v-model="confirmPassword" type="password" placeholder="Confirm Password" style="width: 300px"/>
            </div> -->
            <div>
                <Select v-model="editData.role_id" placeholder="Select Admin Type">
                    <Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{r.roleName}}</Option>
                    <!-- <Option value="Editor">Editor</Option> -->
                </Select>
            </div>
			<div slot="footer">
				<Button type="default" @click="editModal= false">Close</Button>
				<Button type="primary" @click="editAdmin" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing....':'Edit Admin'}}</Button>
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
                fullName: '',
				email: '',
				password: '',
				role_id: null,
			},
			confirmPassword:'',
			users:[],
			addModal:false,
			editModal: false,
			isAdding: false,
			editData:{
				fullName: '',
				email: '',
				password: '',
				role_id: null,
			},
			index : -1,
			deleteModal:false,
			deleteItem: {},
			deletingIndex: -1,
			isDeleting: false,
			roles:[],

		}
	},

	methods: {
		    async addAdmin(){
			if(this.data.fullName.trim()=='') return this.e('Full Name is Required')
            if(this.data.email.trim()=='') return this.e('Email is Required')
            if(this.data.password.trim()=='') return this.e('Password is Required')
            if(!this.data.role_id) return this.e('User Type is Required')
			if(this.data.password!==this.confirmPassword){
				return this.e('Passwords do not match')
				}else{
					this.isAdding = true
					const res = await this.callApi('post', 'app/create_admin', this.data)
					if(res.status=== 201){
						this.users.unshift(res.data)
						this.s('Admin User was created Successfully')
						this.data.fullName = ''
						this.data.email = ''
						this.data.pasword = ''
						this.confirmPassword = ''
						this.addModal = false
						this.isAdding= false
					}else{
						if (res.status===422){
							for(let i in res.data.errors){
								this.e(res.data.errors[i][0])
							}
							}else{
						this.swr()
						}
					}
				}

           
		},
		async editAdmin(){
			if(this.editData.fullName.trim()=='') return this.e('Full Name is Required')
            if(this.editData.email.trim()=='') return this.e('Email is Required')
			if(!this.editData.role_id) return this.e('User Type is Required')
			this.isAdding=true
			const res = await this.callApi('post', 'app/edit_user', this.editData)
			if(res.status===200){
				this.users[this.index] = this.editData
				this.s('User has been edited successfully!')
				this.editModal = false
				this.isAdding= false
				
			}else{
				if(res.status==422){
					for(let i in res.data.errors){
						this.e(res.data.errors[i][0])
					}
					this.isAdding= false
				}else{
					this.swr()
					this.isAdding= false
				}
				
			}

		},
		showEditModal(user, index){
			let obj = {
				id: user.id,
				fullName: user.fullName,
				email: user.email,
				role_id: user.role_id,

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
		showDeletingModal(tag, i){
			const deleteModalObj = {
				showDeleteModal: true,
				deleteUrl:'app/delete_tag',
				data: tag,
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
		const [res, resRole] = await Promise.all([
			this.callApi('get', 'app/get_users', this.users),
			this.callApi('get', 'app/get_roles', this.roles)
		]) 
		if(res.status == 200){
			this.users = res.data
		}else{
			this.swr()
		}
		if(resRole.status == 200){
			this.roles = resRole.data
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
				this.tags.splice(obj.deletingIndex, 1);
			}
		}
	}
}
</script>