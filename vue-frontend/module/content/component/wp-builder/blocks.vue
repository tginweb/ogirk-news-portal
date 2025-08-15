<template>

    <div>

        <component
                :key="index"
                v-bind="bindChild(block)"
                v-for="(block, index) of children"
                v-if="block.blockName"
        />

    </div>

</template>

<script>
    import Base from './base'

    export default {
        extends: Base,
        computed: {}
    }

</script>

<style lang="scss" scoped>


</style>
