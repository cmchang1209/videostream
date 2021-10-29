<template>
    <div class="tournament-live">
        <Breadcrumb :iteams="br" />
        <el-form :model="form" label-position="top">
            <el-form-item :label="$store.state.langData.cont.pageFn.table.Tournament" prop="id">
                <el-select v-model="form.id" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" clearable :disabled="handleDisable" style="width: 100%" @change="handleSelect">
                    <el-option v-for="item in form.data" :key="item.id" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
        </el-form>
        <div v-if="showbracket" class="bracket">
            <f-bracket :data="data" />
        </div>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
import Breadcrumb from '../layout/Breadcrumb.vue'
import Bracket from './Bracket.vue'
export default {
    components: { Breadcrumb, 'f-bracket': Bracket },
    props: ['id'],
    data() {
        return {
            br: [{
                title: this.$store.state.langData.cont.slideMenu.Tournament,
                isUrl: false
            }, {
                title: this.$store.state.langData.cont.slideMenu.Live,
                isUrl: false
            }],
            form: {
                id: '',
                data: []
            },
            data: [],
            showbracket: false
        }
    },
    created() {
        if (this.id) {
            this.form.id = this.id * 1
        }
        this.changeAppLoadingStatus(true)
        this.fetchData()
    },
    computed: {
        handleDisable() {
            return typeof this.id === 'undefined'? false : true
        }
    },
    methods: {
        ...mapActions(['changeAppLoadingStatus']),
        fetchData() {
            axios
                .get('/api/getTournamentListData')
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.form.data = data.data
                        if (this.form.id === '') {
                            this.changeAppLoadingStatus(false)
                        } else {
                            this.getTournamentBracketData()
                        }
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    console.log(error)
                })
        },
        getTournamentBracketData() {
            axios
                .get('/api/getTournamentBracketData', {
                    params: { id: this.form.id }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.changeAppLoadingStatus(false)
                        this.showbracket = true
                        this.data = data.data
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        handleSelect() {
            this.showbracket = false
            if (this.form.id === '') {
                this.data = []
            } else {
                this.changeAppLoadingStatus(true)
                this.getTournamentBracketData()
            }
        }
    }
}

</script>
