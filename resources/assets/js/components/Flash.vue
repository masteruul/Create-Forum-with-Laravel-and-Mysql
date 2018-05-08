<template>
    <div class="alert alert-success alert-flash" role="alert" vshow="show">
        <strong>Succes!</strong>{{body}}
    </div>
</template>

<script>
    export default {
        props:['message'],
        data(){
            return{
                body: this.message,
                show: false
            }
        },

        created(){
            if(this.message){
                this.flash(this.message);
            }

            window.events.$on('flash',message=>{
                this.flash(message);
            });

        },

        methos:{
            flash(message){
                this.body = message;
                this.show = true;

                this.hide();
            },

            hide(){
                setTimeout(()=>{
                    this.show = false;
                },2500);
            }
        }

    };
</script>

<style>
    .alert-flash{
        position:fixed;
        right: 25px;
        bottom: 25px;
    }
</style>