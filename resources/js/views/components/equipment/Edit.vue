<template>
    <div class="equipment-add">
        <Breadcrumb :iteams="br" />
    </div>
</template>
<script>
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    props: ['id'],
    data() {
        return {
            br: [{
                    hasback: true,
                    name: 'EquipmentList',
                    isUrl: false
                },
                {
                    title: this.$store.state.langData.cont.slideMenu.Equipment,
                    isUrl: false
                }, {
                    title: this.$store.state.langData.cont.pageFn.golbal.Edit,
                    isUrl: false
                }
            ],
        }
    },
    created() {},
    methods: {}
}

</script>