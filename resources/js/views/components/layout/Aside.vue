<template>
    <div class="hidden-sm-and-down">
        <!-- 側邊欄菜單區域 -->
        <el-menu unique-opened :collapse="isCollapse" router :default-active="$router.currentRoute.path" :unique-opened="true">
            <div class="toggle-button" @click="isCollapse = !isCollapse">|||</div>
            <el-menu-item index="/">
                <i class="el-icon-setting"></i>
                <span slot="title">
                    {{ $store.state.langData.cont.slideMenu.Equipment }}
                </span>
            </el-menu-item>
            <el-submenu index="2">
                <template slot="title">
                    <i class="el-icon-thirdtournament"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.Tournament }}
                    </span>
                </template>
                <el-menu-item index="/tournament/list">
                    <i class="el-icon-thirdlist"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.List }}
                    </span>
                </el-menu-item>
                <el-menu-item index="/tournament/live">
                    <i class="el-icon-thirdlive"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.Live }}
                    </span>
                </el-menu-item>
            </el-submenu>
            <el-submenu index="3">
                <template slot="title">
                    <i class="el-icon-thirdleague"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.League }}
                    </span>
                </template>
                <el-menu-item index="/league/live">
                    <i class="el-icon-thirdlive"></i>
                    <span slot="title">
                        {{ $store.state.langData.cont.slideMenu.Live }}
                    </span>
                </el-menu-item>
            </el-submenu>
            <el-menu-item index="4">
                <i class="el-icon-thirdlogout"></i>
                <span slot="title">
                    {{ $store.state.langData.cont.slideMenu.Logout }}
                </span>
            </el-menu-item>
        </el-menu>
    </div>
</template>
<script>
export default {
    data() {
        return {
            isCollapse: false
        }
    },
    computed: {}
}

</script>
<style scoped lang="scss">
</style>
