<template>
    <div>
        <Modal :value = "getDeleteModalObj.showDeleteModal" width="360" title="Delete Tag" :mask-closable="false" :closable="false">
            <p slot="header" style="color:#f60;text-align:center">
                <Icon type="ios-information-circle"></Icon>
                    <span>Delete confirmation</span>
            </p>
            <div style="text-align:center">
                <p>{{getDeleteModalObj.msg}}</p>
                        
            </div>
            <div slot="footer">
                <Button type="error" size="large"  :loading="isDeleting" :disabled="isDeleting" @click="deleteCategory" >Delete</Button>
                <Button type="default" size="large"  @click="getDeleteModalObj.showDeleteModal= false">Close </Button>
            </div>
        </Modal>
        </div>    
</template>

<script>
import { mapGetters } from 'vuex'
export default {
    data(){
        return{
            isDeleting: false,
        }
    },
    methods:{
        async deleteCategory(){
            this.isDeleting = true;
            //this.$store.commit('setDeleteModal')
			//if (!confirm('Are You Sure You Want To Delete This Tag?')) return
			//this.$set(tag, 'isDeleting', true)
			const res = await this.callApi('post', this.getDeleteModalObj.deleteUrl, this.getDeleteModalObj.data)
			if (res.status===200) {
                this.s(this.getDeleteModalObj.successMsg)
                this.$store.commit('setDeleteModal', true)
                this.isDeleting = false
			}else{this.swr(); this.$store.commit('setDeleteModal', false)}
		},

    },
    computed:{
        ...mapGetters(['getDeleteModalObj'])
    }
}
</script>