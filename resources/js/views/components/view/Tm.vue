<template>
    <div class="view">
        <div v-if="show" class="view-tm">
            <f-header :name="name" />
            <div v-for="(iteam, index) in pi" :key="index" :class="['video-area', iteam.status ? 'active' : '']" class="video-area">
                <div class="video-2">
                    <f-player :id="iteam.id" :usb="2" />
                </div>
                <div class="video-4">
                    <div class="adr-name">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,386,59">
                            <text x="10" y="38" font-size="24" stroke="#333" stroke-width="6" fill="#333" letter-spacing="3" stroke-linecap="round" stroke-linejoin="round">
                                {{ iteam.p_name }}
                            </text>
                            <text x="10" y="38" font-size="24" fill="#fff" letter-spacing="3">
                                {{ iteam.p_name }}
                            </text>
                        </svg>
                    </div>
                    <f-player :id="iteam.id" :usb="4" />
                </div>
                <div :class="['video-1', active[index] ? 'active' : '']" @click="changePcModel(index)">
                    <f-player :id="iteam.id" :usb="1" />
                </div>
            </div>
            <div :class="['bracket', tree ? 'active' : '']" @click="hindTree">
                <f-bracket :data="data" />
            </div>
            <f-footer v-show="!tree" :data="pi" @changShow="changShowModel" />
        </div>
        <div v-else class="view-tm" style="display: flex; align-items: center; justify-content: center">
            <h1 style="color: white;" v-show="showErrorMsg">無相關賽事</h1>
        </div>
        <div :class="['mask', mask ? 'active' : '']"></div>
        <iframe allow="autoplay" :src="audioSrc" style="display: none;"></iframe>
    </div>
</template>
<script>
import Header from './layout/Header.vue'
import Footer from './layout/Footer.vue'
import Player from './Player.vue'
import Bracket from '../tournament/Bracket.vue'
export default {
    components: {
        'f-player': Player,
        'f-header': Header,
        'f-footer': Footer,
        'f-bracket': Bracket
    },
    props: ['id', 'match'],
    data() {
        return {
            name: '',
            data: [],
            show: false,
            showErrorMsg: false,
            pi: [],
            active: [false, false],
            mask: false,
            tree: false
        }
    },
    created() {
        this.fetchData()
        this.test()
    },
    computed: {
        audioSrc() {
            let pi = 0
            this.pi.map(iteam => {
                if (iteam.status) {
                    pi = iteam.id
                }
            })
            return `http://${document.location.hostname}/view/audio?id=${pi}`
        }
    },
    mounted() {},
    methods: {
        fetchData() {
            if (typeof this.id === 'undefined' || typeof this.match === 'undefined') {
                this.showErrorMsg = true
                return false
            }
            axios
                .get('/api/getTournamentBracketData', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    console.log(data)
                    if (data.errorCode === 'er0000') {
                        this.show = true
                        this.name = data.name
                        this.data = data.data
                        var i = (this.match - 1) * 2 + 1
                        for (var j = i; j <= (i + 1); j++) {
                            var ds = this.data.filter(item => {
                                return j === item.track
                            })
                            var d = {}
                            d.id = ds[0].p_id
                            d.status = j === i ? true : false
                            d.u_name = ds[0].u_name
                            var name = ds[0].p_name.split(" ")
                            if (name.length > 1) {
                                var n = ''
                                for (var k = 0; k <= name.length - 1; k++) {
                                    if (k > 0) {
                                        n += name[k]
                                    }
                                }
                                name = n
                            } else {
                                name = name[0]
                            }
                            d.p_name = name
                            d.track = ds[0].track

                            this.pi.push(d)
                        }
                        console.log(this.pi)
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        changePcModel(i) {
            console.log(i)
            let d = [this.active[0], this.active[1]]
            d[i] = !this.active[i]
            this.mask = !this.mask
            this.active = d
        },
        changShowModel(value) {
            switch (value) {
                case 'home':
                    this.pi[0].status = true
                    this.pi[1].status = false
                    this.tree = false
                    break
                case 'away':
                    this.pi[0].status = false
                    this.pi[1].status = true
                    this.tree = false
                    break
                case 'tree':
                    this.pi[0].status = false
                    this.pi[1].status = false
                    this.tree = true
                    break
            }
        },
        test() {
            let ws = new WebSocket('ws://52.199.5.88:4649/League?name=ethan2')
            ws.onopen = () => {
                var msg = {
                    Type: "Test"
                }
                ws.send(JSON.stringify(msg))
            }

            ws.onmessage = function(e) {
                console.log(e.data)
            }
            ws.onclose = () => {
                console.log('close connection')
            }
        },
        hindTree(value) {
            this.pi[0].status = true
            this.pi[1].status = false
            this.tree = false
        }
    }
}

</script>
