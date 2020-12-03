<template>
    <div>
        <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0"> Category<Button type="dashed" @click="addModal= true" v-if="isWritePermited"><Icon type="md-add-circle"/>Add Category</Button></p>
					

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>Id</th>
								<th>Icon Image</th>
								<th>Category Name</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(category,i) in categoryLists" :key="i" v-if="categoryLists.length">
								<td>{{category.id}}</td>
								<td class="table_image"> <img :src="category.iconImage"/> </td>
								<td class="_table_name">{{category.categoryName}}</td>
								<td>{{category.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(category, i)" v-if="isUpdatePermited">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(category, i)" :loading="category.isDeleting" v-if="isDeletePermited"> Delete</Button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				 <Page :total="100" />

			</div>
		</div>
		<!--tag adding model -->
		<Modal v-model="addModal" title="Add Category"	:mask-closable="false"	:closable="false">

			<Input v-model="data.categoryName" placeholder="Add Category Name" style="width: 300px"/>

            <div class="space" style="padding-bottom: 10px"></div>

             <Upload  ref="upload" type="drag" action="/app/upload" 
			 		:headers="{'x-csrf-token': token,  'X-Requested-With' : 'XMLHttpRequest'}" 
			 		:on-success="handleSuccess"
					:on-error="handleError"
					:format="['jpg','jpeg','png']"
					:on-format-error="handleFormatError"
					:max-size="2048"
					:on-exceeded-size="handleMaxSize">
               <div style="padding: 20px 0">
                    <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"/>
                    <p>Click or drag files here to upload</p>
                </div>
            </Upload>
			<div class="demo-upload-list" v-if="data.iconImage">
				<img :src="`${data.iconImage}`" />
				<div class="demo-upload-list-cover">
					<Icon type="ios-trash-outline"  @click="deleteImage"></Icon>
				</div>
			</div>

			<div slot="footer">
				<Button type="default" @click="addModal= false">Close</Button>
				<Button type="primary" @click="addCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding....':'Add Category'}}</Button>
			</div>				
		</Modal>

			<!--tag editing model -->
		<Modal v-model="editModal" title="Edit Category" :mask-closable="false" :closable="false">
			<Input v-model="editData.categoryName" placeholder="Edit Category Name" style="width: 300px"/>
			<div class="space" style="padding-bottom: 10px"></div>

             <Upload  ref="editDataUploads" type="drag" action="/app/upload" 
			 		v-show="isIconImageNew" 
			 		:headers="{'x-csrf-token': token,  'X-Requested-With' : 'XMLHttpRequest'}" 
			 		:on-success="handleSuccess"
					:on-error="handleError"
					:format="['jpg','jpeg','png']"
					:on-format-error="handleFormatError"
					:max-size="2048"
					:on-exceeded-size="handleMaxSize">
               <div style="padding: 20px 0">
                    <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"/>
                    <p>Click or drag files here to upload</p>
                </div>
            </Upload>
			<div class="demo-upload-list" v-if="editData.iconImage">
				<img :src="`${editData.iconImage}`" />
				<div class="demo-upload-list-cover">
				<Icon type="ios-trash-outline" @click="deleteImage(false)"></Icon>
				</div>
          </div>
			<div slot="footer">
				<Button type="default" @click="closeEditModal">Close</Button>
				<Button type="primary" @click="editCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing....':'Edit Category'}}</Button>
			</div>				
		</Modal> 

		<!-- delete alert modal
		<Modal v-model="deleteModal" width="360" title="Delete Tag" :mask-closable="false" :closable="false">
			<p slot="header" style="color:#f60;text-align:center">
				<Icon type="ios-information-circle"></Icon>
					<span>Delete confirmation</span>
			</p>
			<div style="text-align:center">
				<p>Are you sure you want to delete this category?.</p>
						
			</div>
			<div slot="footer">
				<Button type="error" size="large"  :loading="isDeleting" :disabled="isDeleting" @click="deleteCategory" >Delete</Button>
				<Button type="default" size="large"  @click="deleteModal= false">Close </Button>
			</div>
		</Modal> -->
		<deleteModal></deleteModal>
    </div>
</template>
<script>
import deleteModal from "../components/deleteModalPage";
import {mapGetters, mapActions} from 'vuex'
export default {
	data(){
		return{
			data:{
				iconImage: '',
				categoryName:''
				
			},
			categoryLists:[],
			addModal:false,
			editModal: false,
			isAdding: false,
			editData:{
				iconImage: '',
				categoryName:''
			},
			index : -1,
			deleteModal:false,
			deleteItem: {},
			deletingIndex: -1,
            isDeleting: false,
			token: '',
			isIconImageNew: false,
			isEditingItem: false

		}
	},

	methods: {
		    async addCategory(){
			this.isAdding = true
			if(this.data.categoryName.trim()=='') return this.e('Category name is Required')
			if(this.data.iconImage.trim()=='') return this.e('Icon image is Required')
			this.data.iconImage = `${this.data.iconImage}`
			const res = await this.callApi('post', 'app/create_category', this.data)
			if(res.status=== 201){
				this.categoryLists.unshift(res.data)
				this.s('Category was passed Successfully')
				this.addModal = false
				this.data.categoryName = ''
				this.data.iconImage = ''
				this.isAdding = false
			}else{
				if (res.status===422){
					if(res.data.errors.categoryName){
						this.e(res.data.errors.categoryName[0])
					}
					if(res.data.errors.iconImage){
						this.e(res.data.errors.iconImage[0])
					}else
				this.swr()
				this.addModal = false
				}
			}
		},
		async editCategory(){
			if(this.editData.categoryName.trim()=='') return this.e('Category name is Required')
			if(this.editData.iconImage.trim()=='') return this.e('Icon image is Required')
			this.isAdding = true
			const res = await this.callApi('post', 'app/edit_category', this.editData)
			if(res.status===200){
				this.categoryLists[this.index].categoryName = this.editData.categoryName
				this.s('Category has been edited successfully!')
				this.editModal = false
				this.isAdding = false
				
			}else{
				if(res.status==422){
					if(res.data.errors.categoryName){
						this.e(res.data.errors.categoryName[0])
					}if(res.data.errors.iconImage){
						this.e(res.data.errors.iconImage[0])
					}
					
				}else{
					this.swr()
					this.isAdding= false
				}
				
			}

		},
		showEditModal(category, index){
			// let obj = {
			// 	id: category.id,
			// 	categoryName: category.categoryName,
			// 	iconImage: category.iconImage
			// } 
			// console.log(category)
			this.editData = category
			this.editModal = true
			this.index = index
			this.isEditingItem = true
		},
		// async deleteCategory(category, i){
		// 	//if (!confirm('Are You Sure You Want To Delete This Tag?')) return
		// 	//this.$set(tag, 'isDeleting', true)
		// 	const res = await this.callApi('post', 'app/delete_category', this.deleteItem)
		// 	if (res.status===200) {
		// 		this.categoryLists.splice(this.i, 1)
		// 		this.s('Tag has ben successfully deleted')
		// 		this.deleteModal = false
		// 	}else{this.swr()}
		// },
		showDeletingModal(category, i){
			const deleteModalObj = {
                        showDeleteModal: true,
                        deleteUrl:'app/delete_category',
                        data: category,
                        deletingIndex: i,
						isDeleted: false,
						msg: 'Are you sure you want to delete the category?',
                		successMsg:'Category Deleted Successfully'
				};
			this.$store.commit("setDeletingModalObj", deleteModalObj);
		// console.log('delete method called')
			// this.deleteItem= category,
			// this.i= deletingIndex,
			// this.deleteModal = true

		},
		handleSuccess(res, file){
			res = `/uploads/${res}`;
			if(this.isEditingItem){
				return this.editData.iconImage = res
			}
		this.data.iconImage = res;
		},
		handleError(res, file) {
		this.$Notice.warning({
			title: "The file format is incorrect",
			desc: `${
			file.errors.file.length
				? file.errors.file[0]
				: "Something went wrong!"
			}`
		});
		},
		handleFormatError(file) {
		this.$Notice.warning({
			title: "The file format is incorrect",
			desc:
			"File format of " +
			file.name +
			" is incorrect, please select jpg or png."
		});
		},
		handleMaxSize(file) {
		this.$Notice.warning({
			title: "Exceeding file size limit",
			desc: "File  " + file.name + " is too large, no more than 2M."
		});
		},
		async deleteImage(isAdd = true){
			let image;
			if (!isAdd) {
				// for editing....
						this.isIconImageNew = true;
						image = this.editData.iconImage;
						this.editData.iconImage = "";
						this.$refs.editDataUploads.clearFiles();
					} else {
						image = this.data.iconImage;
						this.data.iconImage = "";
						this.$refs.uploads.clearFiles();
					}
					const res = await this.callApi('post', 'app/delete_image' , {imageName: image})
					if(res.status!=200){
						this.data.iconImage = image
						this.swr()

					}else{this.s('Image was deleted successfully')}
		},
		closeEditModal() {
			this.isEditingItem = false;
			this.editModal = false;
		}
	},

	


	async created(){
        this.token=window.Laravel.csrfToken
		const res = await this.callApi('get', 'app/get_category',)
		if(res.status === 200){
				this.categoryLists = res.data
				// console.log(this.categoryLists)
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
				this.categoryLists.splice(obj.deletingIndex, 1);
			}
		}
	}
}

</script>