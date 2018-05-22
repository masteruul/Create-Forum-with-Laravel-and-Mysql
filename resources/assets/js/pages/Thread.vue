<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default{
        props:['thread'],

        components:{Replies,SubscribeButton},

        data(){
            return{
                repliesCount:this.thread.replies_Count,
                locked: this.thread.locked,
                editing:false,
                title: this.thread.title,
                body: this.thread.body,
                form:{}
            };
        },

        created(){
            this.resetForm();
        },

        methods:{
            
            toggleLock(){
                let uri = `/locked-thread/${this.thread.slug}`;
                axios[
                    this.locked ? 'delete' : 'post'
                ](uri);
                
                this.locked = ! this.locked;
            },

            update(){
                let uri = `/threads/${this.thread.channel.slug}/${this.thread.slug}`;
                axios.patch('/threads/'+this.thread.channel.slug+'/'+this.thread.slug,{
                    title: this.title,
                    body: this.body
                }).then(()=>{
                    this.editing = false,
                    flash('Your thread has been updated.');
                })
            },

            resetForm(){
                this.form={
                    title: this.thread.title,
                    body: this.thread.body
                };

                this.editing = false;
            }

        }
    }
</script>