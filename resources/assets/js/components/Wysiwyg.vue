<template>
    <div>
        <input id="trix" type="hidden" :name="name" value="Write something in here...">
        <trix-editor ref="trix" input="trix">
        </trix-editor>
    </div>
</template>

<script>
    import Trix from 'trix';
    export default{
        props:['name'],

        mounted(){
            this.$refs.trix.addEventListener('trix-change', e=>{
                this.$emit('input', e.target.innerHTML);
            });
        }
    }
</script>
