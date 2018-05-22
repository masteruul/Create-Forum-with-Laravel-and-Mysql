<template>
    <div :id="'reply-'+id" class="col-md-8 col-md-offset-2"> 
        <div class="panel" :class="isBest ? 'panel-success':'panel-default'">
            <div class="panel-heading">
                <div class="level">
                    <h5 class="flex">
                        <a :href="'/profiles/'+data.owner.name"
                            v-text="data.owner.name">
                        </a>
                        said <span v-text="ago"></span>
                    </h5>
                    
                    <div vi-if="signedIn">
                        <favorite :reply="data"></favorite>
                    </div>
                    
                </div>
            </div>

            <div class="panel-body">
                <div v-if="editing">
                    <form @submit="update">
                        <div class="form-group">
                            <wysiwyg v-model="body"></wysiwyg>
                        </div>

                        <button class="btn btn-xs btn-primary">Update</button>
                        <button class="btn btn-xs btn-link" @click="editing=false" type="button">Cancel</button>
                    </form>
                    
                </div>
                <div v-else v-html="body"></div>
            </div>
            <div  class="panel-footer level" v-if="authorize('updateReply', data) || authorize('updateThread',thread)">
                <div v-if="authorize('updateReply', data)">
                    <button class="btn btn-xs mr-1" @click="editing = true" v-if="! editing">Edit</button>
                    <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>                
                </div>
                <button class="btn btn-xs btn-default ml-a" @click="markBestReply" v-if="authorize('updateThread',thread)">Mark Best Reply?</button>                
            </div>
        </div>
    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default{
        props:['data'],

        components:{Favorite},

        data(){
            return{
                editing:false,
                id: this.data.id,
                body: this.data.body,
                reply: this.data,
                thread: window.thread
            };
        },

        computed:{
            isBest(){
                return this.thread.best_reply_id == this.id;
            },

            ago(){
                return moment(this.data.created_at).fromNow();

            }
        },

        methods: {
            update(){
                axios.patch(
                    '/replies/'+this.data.id,{
                    body:this.body
                    })

                    .catch(error=>{
                        flash(error.response.data,'danger');
                    });

                this.editing=false;

                flash('Updated...');
            },

            destroy(){
                axios.delete('/replies/'+this.data.id);

                this.$emit('deleted',this.data.id);

            },

            markBestReply(){
                axios.post('/replies/'+this.data.id+'/best');

                this.thread.best_reply_id = this.id;
            }

        }
    }
</script>