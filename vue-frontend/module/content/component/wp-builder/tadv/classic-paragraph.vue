<template>

    <component v-bind="bind" v-html="innerHTML"/>

</template>

<script>
    import Base from '../base'

    export default {
        extends: Base,
        computed: {
            comTag() {
                return 'div'
            }
        }
    }

</script>

<style lang="scss" scoped>


</style>
