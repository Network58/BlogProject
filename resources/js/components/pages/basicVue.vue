<template>
    <div>
        <h1>The number is : {{counter}}</h1>
        <button @click="updateCounter(+1)">Increase</button>
        <button @click="updateCounter(-1)">Decrease</button>

        <h1 v-if="showItem">True</h1>
        <h1 v-else>False</h1>
        <button @click="showItem=!showItem">Change</button>
    </div>
</template>


<script>
export default {
    data(){
        return{
            counter: 0
        }
       
    },
    
   methods:{
        updateCounter(number){
        return this.counter+=number
    },
    showItem(){
        
    }
   }


}
</script>
