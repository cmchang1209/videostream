<template>
    <div class="view">
        <div v-if="show" class="view-lg">
            <f-header :name="name" :groupName="groupName" :sequence="sequence" :date="date" :type="'l'" :online="online" />
            <div v-for="(iteam, index) in team" :key="index" :class="['video-area', iteam.status ? 'active' : '', index === 0 ? 'home' : 'away', gameStatus ? 'game' : '' ]">
                <template v-if="iteam.pi !== 0">
                    <div class="video-2">
                        <f-player :id="iteam.pi" :usb="2" />
                    </div>
                    <div class="video-4">
                        <f-player :id="iteam.pi" :usb="4" />
                    </div>
                    <div :class="['video-1', active[index] ? 'active' : '']" @click="changePcModel(index)">
                        <f-player :id="iteam.pi" :usb="1" />
                    </div>
                    <div class="adr-name">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,940,79">
                            <text x="50.15%" y="62" font-size="36" stroke="#000" stroke-width="6" fill="#000" text-anchor="middle" letter-spacing="10" stroke-linejoin="round">
                                {{ iteam.storeName }}
                            </text>
                            <text x="50%" y="60" font-size="36" text-anchor="middle" fill="#fff" letter-spacing="10">
                                {{ iteam.storeName }}
                            </text>
                        </svg>
                    </div>
                </template>
                <div v-else style="display: flex; align-items: center; justify-content: center; height: 100%;">
                    <h1 style="color: white;">尚未設定直播設備</h1>
                </div>
            </div>
            <f-footer :data="team" :gameStatus="gameStatus" :first="first" :set="set" :leg="leg" @changShow="changShowModel" />
        </div>
        <div v-else class="view-lg" style="display: flex; align-items: center; justify-content: center">
            <h1 style="color: white;" v-show="showErrorMsg">無相關賽事</h1>
        </div>
        <div :class="['mask', mask ? 'active' : '']"></div>
        <iframe allow="autoplay" :src="audioSrc" style="display: none;"></iframe>
    </div>
</template>
<script>
import Header from './layout/Header.vue'
import Player from './Player.vue'
import Footer from './layout/FooterLg.vue'
export default {
    components: {
        'f-header': Header,
        'f-player': Player,
        'f-footer': Footer,
    },
    props: ['id'],
    sockets: {
        connect() {
            console.log('connect')
        }
    },
    data() {
        return {
            name: '',
            groupName: '',
            sequence: '',
            data: [],
            show: false,
            showErrorMsg: false,
            team: [],
            active: [false, false],
            mask: false,
            online: 0,
            date: new Date(),
            gameStatus: false,
            set: [],
            leg: [],
            first: ''
        }
    },
    created() {
        this.fetchData()
    },
    computed: {
        audioSrc() {
            let pi = 0
            this.team.map(iteam => {
                if (iteam.status) {
                    pi = iteam.pi
                }
            })
            return `http://${document.location.hostname}/view/audio?id=${pi}`
        }
    },
    mounted() {},
    methods: {
        fetchData() {
            if (typeof this.id === 'undefined') {
                this.showErrorMsg = true
                return false
            }
            axios
                .get('/api/getLgViewData', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.show = true
                        this.online = data.data[0].isNetworkGame
                        this.name = data.data[0].leagueName
                        this.groupName = data.data[0].groupName
                        this.sequence = data.data[0].sequence
                        this.date = data.data[0].matchDate
                        this.team = data.team
                        this.runFFmpeg()
                        this.webSocket()
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        changePcModel(i) {
            let d = [this.active[0], this.active[1]]
            d[i] = !this.active[i]
            this.mask = d[i]
            this.active = d
        },
        changShowModel(value) {
            switch (value) {
                case 0:
                    this.team[0].status = true
                    this.team[1].status = false
                    break
                case 1:
                    this.team[0].status = false
                    this.team[1].status = true
                    break
            }
        },
        runFFmpeg() {
            this.team.map(iteam => {
                if (iteam.pi !== 0) {
                    this.$socket.client.emit('runFFmpeg', { id: iteam.pi, usb: 2 })
                    setTimeout(() => {
                        this.$socket.client.emit('runFFmpeg', { id: iteam.pi, usb: 4 })
                        setTimeout(() => {
                            this.$socket.client.emit('runFFmpeg', { id: iteam.pi, usb: 1 })
                        }, 1000)
                    }, 1000)
                }
            })
        },
        webSocket() {
            let urlData = this.$store.state.gobalData.ws
            let ws = new WebSocket(`ws://${urlData.ip}:${urlData.port}/League`)
            ws.onopen = () => {
                var msg = { "cmd": "watch", "battleId": this.id }
                ws.send(JSON.stringify(msg))
            }

            ws.onmessage = (e) => {
                let data = JSON.parse(e.data)
                if (data.errorCode === 'SUCCEED') {
                    if (typeof data.teamDetail !== 'undefined') {
                        if (data.finished.toLowerCase() === 'true') {
                            this.changShowModel(data.currentTeam)
                            this.changePcModel(data.currentTeam)
                        } else {
                            this.changShowModel(data.currentTeam)
                            this.gameStatus = true
                            this.set = data.set
                            this.leg = data.leg
                            this.first = data.first

                            this.team[0].player = data.teamDetail.homeTeamPlayer
                            this.team[1].player = data.teamDetail.awayTeamPlayer

                            this.team[0].row = this.team[1].row = data.teamDetail.currentPlayerRow
                        }
                    }
                }
            }
            ws.onclose = () => {
                console.log('close connection')
            }
        }
    }
}

</script>
