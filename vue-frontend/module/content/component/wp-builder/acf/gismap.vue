<template>

    <div>
        {{attributes}}
    </div>

</template>

<script>
    import Base from './_base'

    export default {
        extends: Base,
        computed: {
            attributes() {
                return this.attrs.data
            }
        }
    }

</script>

<style lang="scss" scoped>


</style>
