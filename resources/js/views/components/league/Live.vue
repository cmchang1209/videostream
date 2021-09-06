<template>
    <div class="league-live">
        <Breadcrumb :iteams="br" />
        <el-form :model="form" label-position="top">
            <el-form-item :label="$store.state.langData.cont.pageFn.table.League" prop="id">
                <el-select v-model="form.id" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable style="width: 100%" @change="handleSelect">
                    <el-option v-for="item in form.data" :key="item.id" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.LeagueGroup" prop="groupId">
                <el-select v-model="form.groupId" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" :no-data-text="$store.state.langData.cont.msg.data.d0001" clearable style="width: 100%" @change="handleGroupSelect">
                    <el-option v-for="item in form.groupData" :key="item.id" :label="item.groupName" :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <br>
        <el-table :data="battleData" stripe style="width: 100%" :empty-text="$store.state.langData.cont.msg.data.d0001">
            <el-table-column prop="sequence" label="場次" width="60">
            </el-table-column>
            <el-table-column prop="matchDate" label="日期" width="110">
            </el-table-column>
            <el-table-column label="主隊" align="center">
                <template slot-scope="scope">
                    <p>{{ scope.row.homeTeamName }}</p>
                    <p>{{ scope.row.homeStoreName }}</p>
                    <el-select v-model="scope.row.homePi" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable style="width: 100%">
                        <el-option v-for="item in pi" :key="item.id" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </template>
            </el-table-column>
            <el-table-column label="客隊" align="center">
                <template slot-scope="scope">
                    <p>{{ scope.row.awayTeamName }}</p>
                    <p>{{ scope.row.awayStoreName }}</p>
                    <el-select v-model="scope.row.awayPi" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable style="width: 100%">
                        <el-option v-for="item in pi" :key="item.id" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </template>
            </el-table-column>
            <el-table-column fixed="right" label="操作" align="right" width="160">
                <template slot-scope="scope">
                    <el-button @click="handleClick(scope.row)" type="text" size="small">綁定直播設備</el-button>
                    <br>
                    <el-button @click.native.prevent="handleCopyUrl(scope.row)" type="text" size="small">拷貝網址</el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    props: ['id', 'groupId'],
    data() {
        return {
            br: [{
                title: this.$store.state.langData.cont.slideMenu.League,
                isUrl: false
            }, {
                title: this.$store.state.langData.cont.slideMenu.Live,
                isUrl: false
            }],
            form: {
                id: '',
                groupId: '',
                data: [],
                groupData: []
            },
            battleData: [],
            pi: []
        }
    },
    created() {
        /*if (typeof this.id !== 'undefined') {
            this.form.id = this.id
        }
        if (typeof this.groupId !== 'undefined') {
            this.form.groupId = this.groupId
        }*/
        this.fetchData()
    },
    methods: {
        ...mapActions(['changeAppLoadingStatus']),
        fetchData() {
            this.changeAppLoadingStatus(true)
            axios
                .get('/api/getLeagueListData', {
                    params: { distributor_id: this.$store.state.gobalData.me.roleID }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.form.data = data.data
                        this.pi = data.pi
                        this.changeAppLoadingStatus(false)
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    console.log(error)
                })
        },
        handleSelect() {
            //console.log('ok')
            //this.$router.push({ name: 'LeagueLive', query: { id: this.form.id } })
            if (this.form.id !== '') {
                this.changeAppLoadingStatus(true)
                axios
                    .get('/api/getLeagueGroupData', {
                        params: { id: this.form.id }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            this.form.groupData = data.data
                            this.changeAppLoadingStatus(false)
                        }
                    }).catch(error => {
                        this.changeAppLoadingStatus(false)
                        console.log(error)
                    })
            } else {
                this.form.groupId = ''
                this.form.groupData = []
            }
        },
        handleGroupSelect() {
            //this.$router.push({ name: 'LeagueLive', query: { id: this.form.id, groupId: this.form.groupId } })
            if (this.form.id !== '' && this.form.groupId !== '') {
                this.changeAppLoadingStatus(true)
                let l = this.form.data.filter(iteam => {
                    return iteam.id === this.form.id
                })
                axios
                    .get('/api/getLeagueBattleData', {
                        params: { id: this.form.id, groupId: this.form.groupId, timezone: l[0].timezone }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            this.battleData = data.data
                            this.changeAppLoadingStatus(false)
                        }
                    }).catch(error => {
                        this.changeAppLoadingStatus(false)
                        console.log(error)
                    })
            }
        },
        handleClick(d) {
            this.changeAppLoadingStatus(true)
            axios
                .post('/api/updateLeaguePiData', d)
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.handleGroupSelect()
                    } else {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database.er0002,
                            type: 'error',
                            offset: 90
                        })
                        this.changeAppLoadingStatus(false)
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    console.log(error)
                })
        },
        handleCopyUrl(d) {
            const el = document.createElement('textarea')
            el.value = `http://${document.location.hostname}/view/lg?id=${d.id}`
            el.setAttribute('readonly', '')
            el.style.position = 'absolute'
            el.style.left = '-9999px'
            document.body.appendChild(el)
            const selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false
            el.select()
            try {
                var successful = document.execCommand('copy')
                this.$message({
                    message: this.$store.state.langData.cont.msg.golbal.g0001,
                    type: 'success',
                    offset: 90
                })
                document.body.removeChild(el)
            } catch (err) {
                this.$message({
                    message: this.$store.state.langData.cont.msg.golbal.g0002,
                    type: 'error',
                    offset: 90
                })
            }
            if (selected) {
                document.getSelection().removeAllRanges()
                document.getSelection().addRange(selected)
            }
        }
    }
}

</script>
