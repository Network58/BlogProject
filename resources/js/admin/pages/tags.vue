<template>
    <div>
        <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Add Tags<Button type="dashed" @click="addModal= true" v-if="isWritePermited"><Icon type="md-add-circle"/>Add Tags</Button></p>
					

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>Id</th>
								<th>Tag Name</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(tag,i) in tags" :key="i" v-if="tags.length">
								<td>{{tag.id}}</td>
								<td class="_table_name">{{tag.tagName}}</td>
								<td>{{tag.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(tag, i)" v-if="isUpdatePermited">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(tag, i)" :loading="tag.isDeleting" v-if="isDeletePermited"> Delete</Button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				 <Page :total="100" />

			</div>
		</div>
		<!--tag adding model -->
		<Modal v-model="addModal" title="Add Tags"	:mask-closable="false"	:closable="false">
			<Input v-model="data.tagName" placeholder="Enter Text" style="width: 300px"/>
			<div slot="footer">
				<Button type="default" @click="addModal= false">Close</Button>
				<Button type="primary" @click="addTag" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding....':'Add Tag'}}</Button>
			</div>				
		</Modal>

			<!--tag editing model -->
		<Modal v-model="editModal" title="Edit Tags" :mask-closable="false" :closable="false">
			<Input v-model="editData.tagName" placeholder="Edit Tag Name" style="width: 300px"/>
			<div slot="footer">
				<Button type="default" @click="editModal= false">Close</Button>
				<Button type="primary" @click="editTag" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing....':'Edit Tag'}}</Button>
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
				tagName: '',
				
			},
			tags:[],
			addModal:false,
			editModal: false,
			isAdding: false,
			editData:{
				tagName: ''
			},
			index : -1,
			deleteModal:false,
			deleteItem: {},
			deletingIndex: -1,
			isDeleting: false,

		}
	},

	methods: {
		    async addTag(){
			this.isAdding = true
			if(this.data.tagName.trim()=='') return this.e('Tagname is Required')
			const res = await this.callApi('post', 'app/create_tag', this.data)
			if(res.status=== 201){
				this.tags.unshift(res.data)
				this.s('Tagname was passed Successfully')
				this.addModal = false
				this.data.tagName = ''
				this.isAdding= false
			}else{
				if (res.status===422){
					if(res.data.errors.tagName){
						this.e(res.data.errors.tagName[0])
					}else
				this.swr()
				}
			}
		},
		async editTag(){
			this.isAdding=true
			if(this.editData.tagName.trim()=='') return this.e('Tag name is required')
			const res = await this.callApi('post', 'app/edit_tag', this.editData)
			if(res.status===200){
				this.tags[this.index].tagName = this.editData.tagName
				this.s('Tag has been edited successfully!')
				this.editModal = false
				this.isAdding= false
				
			}else{
				if(res.status==422){
					if(res.data.errors.tagName){
						this.e(res.data.errors.tagName[0])
					}
					
				}else{
					this.swr()
				}
				
			}

		},
		showEditModal(tag, index){
			let obj = {
				id: tag.id,
				tagName: tag.tagName,
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
			this.deletingIndex = i
			const deleteModalObj = {
				showDeleteModal: true,
				deleteUrl:'app/delete_tag',
				data: tag,
				deletingIndex: i,
				isDeleted: false,
				msg: 'Are you sure you want to delete the tag?',
                successMsg:'Tag Deleted Successfully'
			};
			this.$store.commit("setDeletingModalObj", deleteModalObj);
			console.log('delete method called')
			// this.deleteItem= category,
			// this.i= deletingIndex,
			// this.deleteModal = true

		},
	},
	async created(){
		// console.log(this.isUpdatePermited)
		const res = await this.callApi('get', 'app/get_tags', this.tags)
		if(res.status === 200){
				this.tags = res.data
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
				this.tags.splice(this.deletingIndex, 1);
			}
		}
	}
}
</script>