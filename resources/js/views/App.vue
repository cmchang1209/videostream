<template>
    <div>
        <el-container v-if="!isGuest && !isView" v-loading="appLoading" element-loading-spinner="el-icon-loading">
            <!-- 側邊欄 -->
            <div class="hidden-sm-and-down">
                <Aside />
            </div>
            <!-- 右側內容主體 -->
            <Main />
        </el-container>
        <router-view v-else></router-view>
        <!-- left menu for phone -->
        <el-drawer title="" :visible="drawer" :direction="direction" :size="'100%'" custom-class="hidden-md-and-up" @close="changeDrawerStatus(false)">
            <!-- 側邊欄 -->
            <Aside />
        </el-drawer>
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
            direction: 'ltr'
        }
    },
    created() {
        this.setLangCont(this.text)
        if (localStorage.getItem('id') !== null) {
            var id = Base64.decode(localStorage.getItem('id'))
            var ids = id.split(',')
            this.setMe({
                id: ids[0],
                roleID: ids[1],
                roleCode: ids[2] * 1
            })
            this.changeLoginStatus(false)
        }
        const path = this.$router.currentRoute.path.split('/')
        if (path[1] === 'view') {
            this.changeViewStatus(true)
        } else {
            this.changeViewStatus(false)
        }
    },
    computed: mapState({
        isGuest: state => state.gobalData.isGuest,
        appLoading: state => state.gobalData.appLoading,
        drawer: state => state.gobalData.drawer,
        isView: state => state.gobalData.isView,
    }),
    mounted() {},
    methods: {
        ...mapActions(['setLangCont', 'changeLoginStatus', 'changeDrawerStatus', 'setMe', 'changeViewStatus'])
    },
    watch: {}
}

</script>
<style scoped lang="scss">
</style>
