<template functional>

    <component
        :class="data.staticClass || ''"
        :flat="props.flat"
        :is="$options.components.QBtn"
        :label="props.label"
        @click.prevent
        class=""
        dense
        icon="fas fa-share-alt"
        :size="props.size"
        title="Поделиться в соцсетях"
        v-bind="data.attrs"
    >

        <component
                :is="$options.components.QMenu"
                @hide="parent.controlsOpened = false"
                @show="parent.controlsOpened = true"
        >
            <el-share
                    :sharing="props.sharing"
                    :showLabels="true"
                    caption="Поделиться в соцсетях"
                    class="i-social q-gutter-sm q-pa-sm"
                    orientation="vertical"
            />
        </component>

    </component>

</template>

<script>

    import {QBtn, QMenu} from 'quasar'

    export default {
        functional: true,
        components: {
            QMenu,
            QBtn
        },
        props: {
            sharing: {},
            flat: {default: false},
            label: {},
            size: {default: 'sm'}
        },
    }
</script>

<style lang="scss" scoped>

    .i-social {
        font-size: 14px;
    }

</style>
