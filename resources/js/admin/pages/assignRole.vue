<template>
    <div>
        <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Assign Resources
                        <Select v-model="data.id" placeholder="Select Admin Type" style="width: 400px" @on-change="changeAdmin">
                            <Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{r.roleName}}</Option>
                        </Select>
                    </p>
					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>Resource Name</th>
								<th>Read</th>
								<th>Write</th>
								<th>Update</th>
								<th>Delete</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(r,i) in resources" :key="i" v-if="resources.length">
								<td>{{r.resourceName}}</td>
								<td><Checkbox v-model="r.read"></Checkbox></td>
								<td><Checkbox v-model="r.write"></Checkbox></td>
								<td><Checkbox v-model="r.update"></Checkbox></td>
								<td><Checkbox v-model="r.delete"></Checkbox></td>
							</tr>
                            <div style="padding=left: 300px" class="space">
                                <Button type="primary" :disabled="isAdding" :loading="isAdding" @click="assignRes">Assign</Button>
                            </div>
						</table>
					</div>
				</div>
			</div>
		</div>
    </div>
</template>
<script>
export default {
	data(){
		return{
			data:{
                roleName: '',
                id: null
				
			},
            roles:[],
            isAdding: false,
            resources:[
                {resourceName:'Home', read:false, write:false, delete:false, update:false, name:'/'},
                {resourceName:'Tags', read:false, write:false, delete:false, update:false, name:'tags'},
                {resourceName:'Category', read:false, write:false, delete:false, update:false, name:'category'},
                {resourceName:'Roles', read:false, write:false, delete:false, update:false, name:'roles'},
                {resourceName:'Admin Users', read:false, write:false, delete:false, update:false, name:'adminUsers'},
                {resourceName:'Assign Roles', read:false, write:false, delete:false, update:false, name:'assignRole'},
                {resourceName:'Create Blog', read:false, write:false, delete:false, update:false, name:'createBlog'},
                {resourceName:'Blogs', read:false, write:false, delete:false, update:false, name:'blogs'},
                // {resourceName:'Edit Blog', read:false, write:false, delete:false, update:false, name:'editBlog'},
            
            
            ],
            defaultResources:[
				{resourceName:'Home', read:false, write:false, delete:false, update:false, name:'/'},
                {resourceName:'Tags', read:false, write:false, delete:false, update:false, name:'tags'},
                {resourceName:'Category', read:false, write:false, delete:false, update:false, name:'category'},
                {resourceName:'Roles', read:false, write:false, delete:false, update:false, name:'roles'},
                {resourceName:'Admin Users', read:false, write:false, delete:false, update:false, name:'adminUsers'},
                {resourceName:'Assign Roles', read:false, write:false, delete:false, update:false, name:'assignRole'},
                {resourceName:'Create Blog', read:false, write:false, delete:false, update:false, name:'createBlog'},
                {resourceName:'Blogs', read:false, write:false, delete:false, update:false, name:'blogs'},
                // {resourceName:'Edit Blog', read:false, write:false, delete:false, update:false, name:'editBlog'},
            
            
            ]
			
		}
	},
    methods:{
        async assignRes(){
           	let data = JSON.stringify(this.resources)
			const res = await this.callApi('post','app/assign_roles', {'permission' : data, id: this.data.id})
			if (res.status==200) {
				this.s('Role was passed succsesfully')
				let index = this.roles.findIndex(role => role.id == this.data.id)
				this.roles[index].permissions = data
			}else{
				this.swr()
			}
		},
		
		changeAdmin(){
			let index = this.roles.findIndex(role => role.id == this.data.id)
			let permissions = this.roles[index].permissions
			if(!permissions){
				this.resources = this.defaultResources
			}else{
				this.resources = JSON.parse(permissions)
			}
		}

    },
    

	async created(){
		const res = await this.callApi('get', 'app/get_roles', this.roles)
		if(res.status === 200){
                this.roles = res.data
                if(res.data.length){
                    this.data.id=res.data[0].id
					if(res.data[0].permission){
						// this.resources= this.defaultResources
						this.resources= JSON.parse(res.data[0].permission)
					}
                }
			}else{
				this.swr()
			}
	},

}
</script>