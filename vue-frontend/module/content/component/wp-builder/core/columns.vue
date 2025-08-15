<template>

    <div v-bind="bind">

        <component
                :key="index"
                v-bind="bindChild(block)"
                v-for="(block, index) of children"
        />

    </div>

</template>

<script>
    import Base from '../base'

    export default {
        extends: Base,
        computed: {
            comTag() {
                return 'div'
            },
            comClass() {
                const res = ['wp-block-columns']
                if (this.attrs.verticalAlignment) {
                    res.push('are-vertically-aligned-' + this.attrs.verticalAlignment)
                }
                return res
            },
        }
    }

</script>

<style lang="scss" scoped>


</style>
