<template>

    <component v-bind="bind">


        <component
                :key="index"
                v-bind="bindChild(block)"
                v-for="(block, index) of children"
        />

    </component>

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
                const res = ['wp-block-column']
                if (this.attrs.verticalAlignment) {
                    res.push('is-vertically-aligned-' + this.attrs.verticalAlignment)
                }
                return res
            },
            comStyle() {
                const res = {}
                if (this.attrs.width) {
                    res.flexBasis = this.attrs.width + '%'
                }
                return res
            }
        }
    }

</script>

<style lang="scss" scoped>


</style>
