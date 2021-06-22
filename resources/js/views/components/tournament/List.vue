<template>
    <div>
        <Breadcrumb :iteams="br" />
        <el-row type="flex" justify="end" style="margin-bottom: 15px">
            <el-col :span="6" style="text-align: right;">
                <el-button class="hidden-sm-and-down" type="success" round @click="addTournament">Add New tournament</el-button>
                <el-button class="hidden-md-and-up" type="success" icon="el-icon-plus" circle @click="addTournament"></el-button>
            </el-col>
        </el-row>
    </div>
</template>
<script>
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    props: [],
    data() {
        return {
            br: [{
                title: this.$store.state.langData.cont.slideMenu.Tournament,
                isUrl: false
            }, {
                title: this.$store.state.langData.cont.slideMenu.List,
                isUrl: false
            }]
        }
    },
    methods: {
        addTournament() {
            this.$router.push({ name: 'AddTournament' })
        }
    }
}

</script>
