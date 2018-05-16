<template>
    <div>
        <div class="row col-md-8 col-md-offset-2" >
            <div class="" v-if="signedIn">
                    <div class="form-group">
                        <textarea name="body" 
                                id="body" class="form-control" 
                                placeholder="Have something to say?" 
                                rows="5"
                                required
                                v-model="body"></textarea>
                    </div>
                    <button type="submit" 
                            class="btn btn-default"
                            @click="addReply">Post</button>
            </div>
            
        <p class="text-center" v-else>
            Please <a href="/login">
            sign in</a> to participate in this discussion.
        </p>
        </div>
        
    </div>
</template>

<script>
    import 'jquery.caret';
    import 'at.js';
    
    export default{
        data(){
            return{
                body:''
            };
        },

        mounted() {
            $('#body').atwho({
                at: "@",
                delay: 750,
                callbacks: {
                    remoteFilter: function(query, callback) {
                        $.getJSON("/api/users", {name: query}, function(usernames) {
                            callback(usernames)
                        });
                    }
                }
            });
        },

        methods:{
            addReply(){
                axios.post(location.pathname+'/replies',{body:this.body})
                    .catch(error=>{
                        flash(error.response.data, 'danger');
                    })
                    .then(({data})=>{
                        this.body='';

                        flash('your Replies has been posted..');

                        this.$emit('created',data);
                    });
            }
        }
    }
</script>