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
                        <el-form-item :label="$store.state.langData.cont.pageFn.table.Name">
                            <el-autocomplete ref="name" v-model="player.name" clearable :fetch-suggestions="querySearchAsync" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleSelect" @blur="handleBlur" @clear="handleClear" style="width: 100%"></el-autocomplete>
                        </el-form-item>
                    </el-col>
                    <el-col :span="22" :offset="2">
                        <el-form-item :label="$store.state.langData.cont.pageFn.table.Equipment">
                            <el-autocomplete ref="pi" v-model="player.pi" clearable :fetch-suggestions="querySearchAsyncEquipment" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleSelectEquipment" @blur="handleBlurEquipment" @clear="handleClearEquipment" style="width: 100%"></el-autocomplete>
                        </el-form-item>
                    </el-col>
                </el-row>
                <br>
                <el-table-draggable handle=".handle">
                    <el-table :data="form.players" :empty-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
                        <el-table-column :label="$store.state.langData.cont.pageFn.table.Race_track" width="120">
                            <template slot-scope="scope">
                                <div class="handle">{{ scope.$index+1 }}</div>
                            </template>
                        </el-table-column>
                        <el-table-column :label="$store.state.langData.cont.pageFn.table.Name">
                            <template slot-scope="scope">
                                <div class="handle">{{ scope.row.u_name }}</div>
                            </template>
                        </el-table-column>
                        <el-table-column :label="$store.state.langData.cont.pageFn.table.Equipment">
                            <template slot-scope="scope">
                                <div class="handle">{{ scope.row.p_name }}</div>
                            </template>
                        </el-table-column>
                        <el-table-column fixed="right" :label="$store.state.langData.cont.pageFn.table.Operating" width="100">
                            <template slot-scope="scope">
                                <el-button @click.native.prevent="handleDelete(scope.$index, scope.row)" type="text">
                                    {{ $store.state.langData.cont.pageFn.table.Delete }}
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-table-draggable>
            </el-form-item>
            <!-- <el-form-item :label="$store.state.langData.cont.pageFn.table.LeagueGroup">
                <el-row>
                    <el-col :span="22" :offset="2">
                        <el-form-item :label="$store.state.langData.cont.pageFn.table.Name">
                            <el-input v-model="group.name" clearable></el-input>
                        </el-form-item>
                        <el-form-item :label="$store.state.langData.cont.pageFn.table.TournamentGroupType" prop="id">
                            <el-select v-model="group.type" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" :no-data-text="$store.state.langData.cont.msg.data.d0001" clearable style="width: 100%">
                                <el-option v-for="item in $store.state.langData.cont.pageFn.tournament.type" :key="item.id" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form-item> -->
            <el-form-item>
                <el-button type="primary" @click.native.prevent="add" :loading="loading">
                    {{ $store.state.langData.cont.pageFn.golbal.Add }}
                </el-button>
                <el-button @click.native.prevent="cancel">
                    {{ $store.state.langData.cont.pageFn.golbal.Cancel }}
                </el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
import Breadcrumb from '../layout/Breadcrumb.vue'
import ElTableDraggable from 'element-ui-el-table-draggable'
export default {
    components: { Breadcrumb, ElTableDraggable },
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
                }
            },
            group: {
                name: '',
                type: ''
            },
            form: {
                name: '',
                players: [],
                group: []
            },
            loading: false,
            rules: {
                name: [
                    { validator: validateName, required: true, trigger: 'no' }
                ]
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
                    axios
                        .post('/api/addTournament', {
                            data: this.form
                        })
                        .then(response => {
                            let data = response.data.data
                            if (data.errorCode === 'er0000') {
                                this.$message({
                                    message: this.$store.state.langData.cont.msg.database.ok0001,
                                    type: 'success',
                                    offset: 90
                                })
                                this.$router.replace({ name: 'TournamentList' })
                            } else {
                                this.$message({
                                    message: this.$store.state.langData.cont.msg.database[data.errorCode],
                                    type: 'error',
                                    offset: 90
                                })
                            }
                        }).catch(error => {
                            this.loading = false
                            this.$message({
                                message: JSON.stringify(error),
                                type: 'error',
                                offset: 90
                            })
                        })
                } else {
                    return false
                }
            })
        },
        cancel() {
            this.$router.replace({ name: 'TournamentList' })
        },
        querySearchAsync(queryString, cb) {
            var restaurants = []
            if (queryString && queryString.length >= 1 && !(queryString === '' || queryString.trim() === '')) {
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
        handleSelect(item) {
            this.player.add.u_id = item.id
            this.player.add.u_name = item.value
            this.addPlayer()
        },
        handleBlur() {
            this.player.name = this.player.add.u_name
        },
        handleClear() {
            this.$refs.name.$el.getElementsByTagName('input')[0].blur()
            this.player.name = this.player.add.u_name = ''
            this.player.add.u_id = ''
        },
        querySearchAsyncEquipment(queryString, cb) {
            var restaurants = []
            if (queryString && queryString.length >= 1 && !(queryString === '' || queryString.trim() === '')) {
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
            this.player.add.p_id = item.id
            this.player.add.p_name = item.value
            this.addPlayer()
        },
        handleBlurEquipment() {
            this.player.pi = this.player.add.p_name
        },
        handleClearEquipment() {
            this.$refs.pi.$el.getElementsByTagName('input')[0].blur()
            this.player.pi = this.player.add.p_name = ''
            this.player.add.p_id = ''
        },
        addPlayer() {
            if (this.player.name !== '' && this.player.pi !== '') {
                if (this.form.players.length < 8) {
                    let copy = Object.assign({}, this.player.add)
                    this.form.players.push(copy)
                    this.handleClear()
                    this.handleClearEquipment()
                } else {
                    this.$message({
                        message: this.$store.state.langData.cont.msg.validate.er0100,
                        type: 'error',
                        offset: 90
                    })
                    this.handleClear()
                    this.handleClearEquipment()
                }
            }
        }
    },
    handleDelete(index, row) {
        this.form.players.splice(index, 1)
    },
    test() {
        console.log(this.group.name)
    }
}

</script>
