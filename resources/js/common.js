import {mapGetters} from 'vuex'
import { stubTrue } from 'lodash';

export default {
    data(){
        return{

        }
    },

    methods:{

        async callApi(method, url, dataObj ){
            try {
              return await axios({
                    method: method,
                    url: url,
                    data: dataObj
                });
            } catch (e) {
                return e.response
            }
        }, 

        i(desc, title="Info"){
           this.$Notice.info({
               title: title,
               desc: desc
           });
        },
        s(desc, title="Great!"){
            this.$Notice.success({
                title: title,
                desc: desc
            });
         },
        w(desc, title="Oops"){
            this.$Notice.warning({
                title: title,
                desc: desc
            });
         },
        e (desc, title="Oowww!"){
            this.$Notice.error({
                title: title,
                desc: desc
            });
         },
        swr(desc="Something went wrong! Please try again", title="Try Again"){
            this.$Notice.error({
                title: title,
                desc: desc
            });
         },
         checkForUserPermission(key){
            if(!this.userPermission) return true
            let isPermited = false
            for(let d of this.userPermission) {
                if(this.$route.name==d.name){
                    if(d[key]== true){
                        isPermited = true
                        break;   
                    }else{
                        break;
                    }
                }
            }
            return isPermited
        }
    },
    computed: {
        ...mapGetters({
            'userPermission' : 'getUserPermission'
        }),
        isReadPermited(){
            return this.checkForUserPermission('read')
        },
        isWritePermited(){
            // console.log(this.userPermission)
            return this.checkForUserPermission('write')
        },
        isDeletePermited(){
            return this.checkForUserPermission('delete')
        },
        isUpdatePermited(){
            return this.checkForUserPermission('update')
        }
        
    },
}