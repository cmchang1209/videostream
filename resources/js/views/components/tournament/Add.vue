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
                            <el-autocomplete v-model="form.players[0].name" :fetch-suggestions="querySearchAsync" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleSelect" style="width: 100%"></el-autocomplete>
                        </el-form-item>
                    </el-col>
                    <el-col :span="22" :offset="2">
                        <el-form-item :label="$store.state.langData.cont.pageFn.table.Equipment">
                            <el-autocomplete v-model="form.players[0].pi" :fetch-suggestions="querySearchAsyncEquipment" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleSelectEquipment" style="width: 100%"></el-autocomplete>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-table :data="form.players" style="width: 100%">
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Race_track" width="120">
                        <template v-if="scope.$index === 0" slot-scope="scope">
                        </template>
                    </el-table-column>
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Name" width="400">
                        <template v-if="scope.$index === 0" slot-scope="scope">
                        </template>
                    </el-table-column>
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Equipment">
                        <template v-if="scope.$index === 0" slot-scope="scope">
                            <el-input :placeholder="$store.state.langData.cont.msg.placeholder.ph0001"></el-input>
                            <el-input :placeholder="$store.state.langData.cont.msg.placeholder.ph0001"></el-input>
                        </template>
                    </el-table-column>
                    <el-table-column fixed="right" label="操作" width="100">
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
            form: {
                name: '',
                players: [{
                    track: null,
                    name: '',
                    pi: null
                }, {
                    name: '',
                    pi: null
                }, {
                    name: '',
                    pi: null
                }, {
                    name: '',
                    pi: null
                }, {
                    name: '',
                    pi: null
                }, {
                    name: '',
                    pi: null
                }, {
                    name: '',
                    pi: null
                }, {
                    name: '',
                    pi: null
                }, {
                    name: '',
                    pi: null
                }]
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
    computed: {
        track() {
            let data = [1, 2, 3, 4, 5, 6, 7, 8]
            let selectData = []
            data.map((iteam, index) => {
                if (this.form.players[iteam].name === '') {
                    selectData.push(iteam)
                }
            })
            this.form.players[0].track = selectData[0]
            return selectData
        }
    },
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

        },
        querySearchAsync(queryString, cb) {
            var restaurants = []
            if (queryString.length >= 3) {
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
                        console.log(error)
                    })
            } else {
                cb(restaurants)
            }
        },
        handleSelect() {

        },
        querySearchAsyncEquipment(queryString, cb) {
            var results = []
            if (queryString === null || queryString === '' || queryString.trim() === '') {
                cb([])
            } else {
                axios
                    .get('/api/getEquipmentData', {
                        params: { distributor_id: this.$store.state.gobalData.me.roleID }
                    })
                    .then(response => {
                        let data = response.data.data
                        var restaurants = []
                        if (data.errorCode === 'er0000') {
                            data.equipments.map(iteam => {
                                if (this.$store.state.gobalData.me.roleID === null || this.$store.state.gobalData.me.roleID === '' || iteam.distributor_id === this.$store.state.gobalData.me.roleID) {
                                    var opt = {}
                                    var store = data.store[iteam.store_id]
                                    iteam.value = `${iteam.name}`
                                    if(store) {
                                        iteam.value = `${iteam.name} ${store.name} ${store.fidoStoreId}`
                                    }
                                    opt.value = iteam.value
                                    opt.id = iteam.id
                                    restaurants.push(opt)
                                }
                            })
                            results = queryString ? restaurants.filter(this.createStateFilter(queryString)) : restaurants
                            cb(results)
                        }
                    }).catch(error => {
                        cb(results)
                        console.log(error)
                    })
            }
        },
        createStateFilter(queryString) {
            return (state) => {
                return (state.value.toLowerCase().indexOf(queryString.toLowerCase()) !== -1);
            }
        },
        handleSelectEquipment() {

        }
    }
}

</script>
