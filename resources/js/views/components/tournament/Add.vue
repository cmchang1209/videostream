<template>
    <div class="tournament-add">
        <Breadcrumb :iteams="br" />
        <el-form :model="form" :rules="rules" ref="ruleForm" label-position="top">
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Name" prop="name">
                <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Players">
                <el-table :data="form.players" style="width: 100%">
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Race_track">
                        <template v-if="scope.$index === 0" slot-scope="scope">
                            <el-select v-model="form.players[0].track" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" style="width: 100%" :no-data-text="$store.state.langData.cont.msg.data.d0001" :no-match-text="$store.state.langData.cont.msg.data.d0002">
                                <el-option v-for="item in track" :key="item" :label="item" :value="item">
                                </el-option>
                            </el-select>
                        </template>
                    </el-table-column>
                    <el-table-column :label="$store.state.langData.cont.pageFn.table.Name">
                        <template v-if="scope.$index === 0" slot-scope="scope">
                            <!-- <el-select v-model="form.players[0].name" multiple filterable remote :multiple-limit="1" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" :remote-method="getPlayer" :loading="playerOptions.loading" :loading-text="$store.state.langData.cont.msg.data.d0003" :no-data-text="$store.state.langData.cont.msg.data.d0001" :no-match-text="$store.state.langData.cont.msg.data.d0002" style="width: 100%">
                                <el-option v-for="item in getPlayerOptions" :key="item.id" :label="item.label" :value="item.id">
                                    <span style="float: left">{{ item.label }}</span>
                                    <span style="float: right; color: #8492a6; font-size: 13px">{{ item.fidoID }}</span>
                                </el-option>
                            </el-select> -->
                            <el-autocomplete v-model="form.players[0].name" :fetch-suggestions="querySearchAsync" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleSelect" style="width: 100%"></el-autocomplete>
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
            },
            playerOptions: []
        }
    },
    created() {
        this.fetchData()
    },
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
        },
        /*getPlayerOptions() {
            this.playerOptions.data.map(iteam => {
                iteam.label = ''
                if (this.isBase64(iteam.name)) {
                    iteam.label = `${Base64.decode(iteam.name)}`
                }
                if (this.isBase64(iteam.nickName)) {
                    if (iteam.label === '') {
                        iteam.label = `${Base64.decode(iteam.nickName)}`
                    } else {
                        iteam.label = `${iteam.label}(${Base64.decode(iteam.nickName)})`
                    }
                }
            })
            return this.playerOptions.data
        }*/
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
        fetchData() {
            axios
                .get('/api/getTournamentData')
                .then(response => {
                    let data = response.data.data
                    data.players.map(iteam => {
                        var opt = {}
                        iteam.value = ''
                        if (this.isBase64(iteam.name)) {
                            iteam.name = `${Base64.decode(iteam.name)}`
                            iteam.value = iteam.name
                        }
                        if (this.isBase64(iteam.nickName)) {
                            iteam.nickName = `${Base64.decode(iteam.nickName)}`
                            if (iteam.value === '') {
                                iteam.value = iteam.nickName
                            } else {
                                if (iteam.name !== iteam.nickName) {
                                    iteam.value = `${iteam.value}(${iteam.nickName})`
                                }
                            }
                        }

                        iteam.value = `${iteam.value} ${iteam.fidoID}`
                        opt.value = iteam.value
                        opt.id = iteam.id
                        this.playerOptions.push(opt)
                    })
                    console.log(this.playerOptions)
                }).catch(error => {
                    console.log(error)
                })
        },
        querySearchAsync(queryString, cb) {
            var restaurants = this.playerOptions
            var results = queryString ? restaurants.filter(this.createStateFilter(queryString)) : restaurants
            cb(results)
        },
        createStateFilter(queryString) {
            return (restaurants) => {
                return (restaurants.value.toLowerCase().indexOf(queryString.toLowerCase()) !== -1)
            }
        },
        handleSelect() {

        },
        isBase64(str) {
            if (str === '' || str.trim() === '') {
                return false
            }
            try {
                return btoa(atob(str)) == str
            } catch (err) {
                return false
            }
        }
    }
}

</script>
