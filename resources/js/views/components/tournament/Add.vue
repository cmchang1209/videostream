<template>
    <div class="tournament-add">
        <Breadcrumb :iteams="br" />
        <el-form :model="form" :rules="rules" ref="ruleForm" label-position="top">
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Name" prop="name">
                <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Players">
                <el-row>
                    <el-col :span="22" :offset="2">
                        <!-- <el-form-item :label="$store.state.langData.cont.pageFn.table.Name">
                            <el-autocomplete v-model="player.name" :fetch-suggestions="querySearchAsync" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleSelect" @change="handleSelect" style="width: 100%"></el-autocomplete>
                        </el-form-item> -->
                        <el-select v-model="player.name" filterable remote clearable :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" :remote-method="remoteMethod" @select="handleSelect" :loading="player.loading" style="width: 100%" @clear="player.opt=[]" :loading-text="$store.state.langData.cont.msg.data.d0003" :no-match-text="$store.state.langData.cont.msg.data.d0002" :no-data-text="$store.state.langData.cont.msg.data.d0001" @click.native="handleClick">
                            <el-option v-for="item in player.opt" :key="item.id" :label="item.value" :value="item.id">
                            </el-option>
                        </el-select>
                    </el-col>
                    <el-col :span="22" :offset="2">
                        <el-form-item :label="$store.state.langData.cont.pageFn.table.Equipment">
                            <el-autocomplete v-model="player.pi" clearable placement="top-start" :fetch-suggestions="querySearchAsyncEquipment" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" :no-match-text="$store.state.langData.cont.msg.data.d0002" :no-data-text="$store.state.langData.cont.msg.data.d0001" @select="handleSelectEquipment" @blur="handleBlurEquipment" @clear="handleClearEquipment" style="width: 100%"></el-autocomplete>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-table :data="form.players" :empty-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Race_track" width="120">
                        <template slot-scope="scope">
                        </template>
                    </el-table-column>
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Name" width="400">
                        <template slot-scope="scope">
                        </template>
                    </el-table-column>
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Equipment">
                        <template slot-scope="scope">
                        </template>
                    </el-table-column>
                    <el-table-column fixed="right" :label="$store.state.langData.cont.pageFn.table.Operating" width="100">
                        <template slot-scope="scope"></template>
                    </el-table-column>
                </el-table>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="add" :loading="loading">
                    {{ $store.state.langData.cont.pageFn.golbal.Add }}
                </el-button>
                <el-button @click="cancel">
                    {{ $store.state.langData.cont.pageFn.golbal.Cancel }}
                </el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    props: [],
    data() {
        var validateName = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error(this.$store.state.langData.cont.msg.validate.er0050))
            } else {
                callback()
            }
        }
        return {
            br: [{
                    hasback: true,
                    name: 'TournamentList',
                    isUrl: false
                },
                {
                    title: this.$store.state.langData.cont.slideMenu.Tournament,
                    isUrl: false
                }, {
                    title: this.$store.state.langData.cont.pageFn.golbal.Add,
                    isUrl: false
                }
            ],
            player: {
                name: '',
                pi: '',
                add: {
                    u_id: '',
                    u_name: '',
                    p_id: '',
                    p_name: ''
                },
                opt: [],
                loading: false
            },
            form: {
                name: '',
                players: []
            },
            loading: false,
            rules: {
                name: [
                    { validator: validateName, required: true, trigger: 'no' }
                ],
            }
        }
    },
    created() {},
    computed: {},
    methods: {
        add() {
            this.$refs['ruleForm'].validate((valid) => {
                if (valid) {
                    this.loading = true
                } else {
                    return false
                }
            })
        },
        cancel() {
            this.$router.replace({ name: 'TournamentList' });
        },
        querySearchAsync(queryString, cb) {
            var restaurants = []
            if (queryString.length >= 3 && !(queryString === '' || queryString.trim() === '')) {
                axios
                    .get('/api/getPlayersData', {
                        params: { name: queryString }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            data.players.map(iteam => {
                                var opt = {}
                                opt.value = `${Base64.decode(iteam.value)}`
                                opt.id = iteam.id
                                restaurants.push(opt)
                            })
                            cb(restaurants)
                        }
                    }).catch(error => {
                        cb(restaurants)
                        console.log(error)
                    })
            } else {
                cb(restaurants)
            }
        },
        querySearchAsyncEquipment(queryString, cb) {
            var restaurants = []
            if (!this.player.readonly && queryString && queryString.length >= 1 && !(queryString === '' || queryString.trim() === '')) {
                axios
                    .get('/api/getEquipmentData', {
                        params: { distributor_id: this.$store.state.gobalData.me.roleID, name: queryString }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            data.equipments.map(iteam => {
                                var opt = {}
                                opt.value = iteam.value
                                opt.id = iteam.id
                                restaurants.push(opt)
                            })
                            cb(restaurants)
                        }
                    }).catch(error => {
                        cb(restaurants)
                        console.log(error)
                    })
            } else {
                cb(restaurants)
            }
        },
        handleSelectEquipment(item) {
            console.log(this.player)
            this.player.add.p_id = item.id
            this.player.add.p_name = item.value
            //this.player.readonly = true
        },
        remoteMethod(query) {
            console.log(query)
            var restaurants = []
            if (query.length >= 1 && !(query === '' || query.trim() === '')) {
                this.player.loading = true
                axios
                    .get('/api/getPlayersData', {
                        params: { name: query }
                    })
                    .then(response => {
                        let data = response.data.data
                        if (data.errorCode === 'er0000') {
                            data.players.map(iteam => {
                                let value = `${Base64.decode(iteam.value)}`
                                if (value.toLowerCase().indexOf(query.toLowerCase()) > -1) {
                                    var opt = {}
                                    opt.value = value
                                    opt.id = iteam.id
                                    restaurants.push(opt)
                                }

                            })
                            this.player.opt = restaurants
                            this.player.loading = false
                        }
                    }).catch(error => {
                        console.log(error)
                    })
            } else {
                this.player.opt = restaurants
            }
        },
        handleSelect() {

        },
        handleBlurEquipment(event) {
            console.log(event)
            this.player.pi = this.player.add.p_name
        },
        handleClearEquipment() {
            this.player.pi = this.player.add.p_name = ''
            this.player.add.p_id = ''
        },
        addPlayer() {}
    }
}

</script>
