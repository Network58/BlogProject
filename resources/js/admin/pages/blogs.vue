<template>
    <div>
        <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Add Blogs<Button type="dashed" @click="$router.push('/createBlog')" v-if="isWritePermited"><Icon type="md-add-circle"/>Add Blogs</Button></p>
					

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>Id</th>
								<th>Title</th>
								<th>Blog Tags</th>
								<th>Blog Categories</th>
								<th>Views</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(b,i) in blogs" :key="i" v-if="blogs.length">
								<td>{{b.id}}</td>
								<td class="_table_name">{{b.title}}</td>
								<td> <span v-for="(t, j) in b.tag" v-if="b.tag.length"><Tag type="border">{{t.tagName}}</Tag></span></td>
								<td> <span  v-for="(c, j) in b.cat" v-if="b.cat.length"><Tag type="border">{{c.categoryName}}</Tag></span></td>
								<td>{{b.views}}</td>
								<td>
									<Button type="info" size="small">View Blog</Button>
									<Button type="info" size="small" @click="$router.push(`/editblog/${b.id}`)" v-if="isUpdatePermited">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(b, i)" :loading="b.isDeleting" v-if="isDeletePermited"> Delete</Button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				 <Page :total="100" />

			</div>
		</div>
		<deleteModal/>
    </div>
</template>
<script>
import deleteModal from '../components/deleteModalPage'
import {mapGetters} from 'vuex'
export default {
	data(){
		return{
			blogs:[],
			index : -1,
			deleteModal:false,
			deleteItem: {},
			deletingIndex: -1,
			isDeleting: false,

		}
	},

	methods: {
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
		showDeletingModal(b, i){
            this.deletingIndex = i
			const deleteModalObj = {
				showDeleteModal: true,
				deleteUrl:'app/delete_blog',
				data: b,
				deletingIndex: i,
                isDeleted: false,
                msg: 'Are you sure you want to delete the blog?',
                successMsg:'Blog Deleted Successfully'
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
		const res = await this.callApi('get', 'app/get_blogs')
		if(res.status === 200){
                this.blogs = res.data
                console.log(this.blogs)
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
				this.blogs.splice(this.deletingIndex, 1);
			}
		}
	}
}
</script>