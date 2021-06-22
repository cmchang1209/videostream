<template>
    <div>
        <el-container v-if="!isGuest" v-loading="appLoading" element-loading-spinner="el-icon-loading">
            <!-- 側邊欄 -->
            <Aside />
            <!-- 右側內容主體 -->
            <Main />
        </el-container>
        <router-view v-else></router-view>
    </div>
</template>
<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import Aside from './components/layout/Aside.vue'
import Main from './components/layout/Main.vue'
export default {
    components: { Aside, Main },
    props: ['text'],
    data() {
        return {

        }
    },
    created() {
        this.setLangCont(this.text)
        if (sessionStorage.getItem('id') !== null) {
            this.changeLoginStatus(false)
        }
    },
    computed: mapState({
        isGuest: state => state.gobalData.isGuest,
        appLoading: state => state.gobalData.appLoading,
    }),
    mounted() {},
    methods: {
        ...mapActions(['setLangCont', 'changeLoginStatus'])
    },
    watch: {}
}

</script>
<style scoped lang="scss">
</style>
