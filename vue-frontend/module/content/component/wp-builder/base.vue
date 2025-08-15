<template>

    <div class="com">


    </div>

</template>

<script>

    export default {
        props: {
            children: {},
            attrs: {},
            innerHTML: {}
        },
        data() {
            return {}
        },
        computed: {
            bind() {
                const res = {}

                if (this.comTag)
                  res.is = this.comTag

                res.class = this.comClass
                res.style = this.comStyle
                return res
            },
            comTag() {
                return null
            },
            comClass() {
                return []
            },
            comStyle() {
                return {}
            },
        },
        methods: {
            bindChild(block) {
                const res = {}
                res.is = this.comName(block)
                res.children = block.innerBlocks
                res.attrs = block.attrs
                res.innerHTML = block.innerHTML
                return res
            },
            comName(block) {
                let type = block.blockName.replace(/[\/]/, '-')

                const schema = {
                    'core-columns' : {},
                    'core-column' : {},
                    'core-heading' : {},
                    'core-paragraph' : {},
                    'tadv-classic-paragraph' : {},
                    'acf-gismap' : {},
                }

                if (!schema[type]) {
                    type = 'common'
                }

                const com = 'wp-block-' + type

                return com
            }
        }
    }

</script>

<style lang="scss" scoped>


</style>
