<template>
    <div class="view no-user-select">
        <div v-if="show" class="view-lg">
            <f-header :name="name" :groupName="groupName" :sequence="sequence" :date="date" :type="'l'" :online="online" />
            <!-- <div v-for="(iteam, index) in team" :key="index" :class="['video-area', iteam.status[0] ? 'active' : '', index === 0 ? 'home' : 'away', 'game' ]"> -->
            <div v-for="(iteam, index) in team" :key="index" :class="['video-area', iteam.status[0] ? 'active' : '', boredrColor, 'game' ]">
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
            gameFinished: false,
            pcMode: false,
            set: [],
            leg: [],
            first: '',
            autoRun: null,
            autoRunCt: 1,
            gameCt: 0,
            audio: 0
        }
    },
    created() {
        this.fetchData()
    },
    computed: {
        audioSrc() {
            let pi = 0
            if (this.team.length > 0) {
                if (this.team[0].pi !== 0 && this.team[1].pi !== 0) {
                    pi = this.team[this.audio].pi
                } else {
                    this.team.map(iteam => {
                        if (iteam.pi !== 0) {
                            pi = iteam.pi
                        }
                    })
                }
            }
            return `http://${document.location.hostname}/view/audio?id=${pi}`
        },
        boredrColor() {
            let str = ''
            if (this.team[0].pi !== 0 && this.team[1].pi !== 0) {
                str = this.team[0].status[1] ? 'home' : 'away'
            }
            return str
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
                        this.audio = data.audio
                        this.team = data.team
                        this.runFFmpeg()
                        this.webSocket()
                        if (!this.gameStatus) {
                            this.autoRun = setInterval(() => {
                                this.changShowModel(this.autoRunCt)
                                this.autoRunCt = this.autoRunCt === 0 ? 1 : 0
                            }, 6000)
                        }
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        changePcModel(i) {
            let d = [this.active[0], this.active[1]]
            d[i] = !this.active[i]
            this.mask = d[i]
            this.pcMode = d[i]
            if (!this.pcMode) {
                if (!this.gameStatus) {
                    this.changShowModel(this.autoRunCt)
                } else {
                    this.changShowModel(this.gameCt)
                }
            } else {
                this.autoRunCt = i
            }
            this.active = d
        },
        changShowModel(value) {
            /* 不是 pc 放大模式 */
            if (!this.pcMode) {
                /* 等待模式 */
                if (!this.gameStatus) {
                    switch (value) {
                        case 0:
                            this.team[0].status = [true, true]
                            this.team[1].status = [false, false]
                            break
                        case 1:
                            this.team[0].status = [false, false]
                            this.team[1].status = [true, true]
                            break
                    }
                } else {
                    /* 綁定2個PI */
                    if (this.team[0].pi !== 0 && this.team[1].pi !== 0) {
                        /* 2個PI不相等 */
                        if (this.team[0].pi !== this.team[1].pi) {
                            switch (value) {
                                case 0:
                                    this.team[0].status = [true, true]
                                    this.team[1].status = [false, false]
                                    break
                                case 1:
                                    this.team[0].status = [false, false]
                                    this.team[1].status = [true, true]
                                    break
                            }
                            /* 2個PI相等 */
                        } else {
                            switch (value) {
                                case 0:
                                    this.team[0].status = [true, true]
                                    this.team[1].status = [false, false]
                                    break
                                case 1:
                                    this.team[0].status = [true, false]
                                    this.team[1].status = [false, true]
                                    break
                            }
                        }
                        /* 綁定<1個PI */
                    } else {
                        let i = ''
                        this.team.map((iteam, index) => {
                            if (iteam.pi !== 0) {
                                i = index
                            }
                        })
                        if (i === 0) {
                            switch (value) {
                                case 0:
                                    this.team[0].status = [true, true]
                                    this.team[1].status = [false, false]
                                    break
                                case 1:
                                    this.team[0].status = [true, false]
                                    this.team[1].status = [false, true]
                                    break
                            }
                        } else if (i === 1) {
                            switch (value) {
                                case 0:
                                    this.team[0].status = [false, true]
                                    this.team[1].status = [true, false]
                                    break
                                case 1:
                                    this.team[0].status = [false, false]
                                    this.team[1].status = [true, true]
                                    break
                            }
                        }
                    }
                }
            } else {
                switch (value) {
                    case 0:
                        this.team[0].status = [this.team[0].status[0], true]
                        this.team[1].status = [this.team[1].status[0], false]
                        break
                    case 1:
                        this.team[0].status = [this.team[0].status[0], false]
                        this.team[1].status = [this.team[1].status[0], true]
                        break
                }
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
            //let urlData = this.$store.state.gobalData.wsTest1
            //let urlData = this.$store.state.gobalData.wsTest2
            let ws = new WebSocket(`ws://${urlData.ip}:${urlData.port}/League`)
            ws.onopen = () => {
                var msg = { "cmd": "watch", "battleId": this.id }
                ws.send(JSON.stringify(msg))
            }

            ws.onmessage = (e) => {
                let data = JSON.parse(e.data)
                if (data.errorCode === 'SUCCEED') {
                    if (typeof data.teamDetail !== 'undefined') {
                        if (typeof data.finished !== 'undefined' && data.finished.toLowerCase() === 'true') {
                            if (this.team[data.currentTeam].pi !== 0) {
                                this.changShowModel(data.currentTeam)
                                this.changePcModel(data.currentTeam)
                            } else {
                                let i = data.currentTeam === 0 ? 1 : 0
                                this.changShowModel(i)
                                this.changePcModel(i)
                            }
                        }
                        setTimeout(() => {
                            if (this.autoRun !== null) {
                                clearInterval(this.autoRun)
                                this.autoRun = null
                            }
                            this.gameStatus = true
                            this.set = data.set
                            this.leg = data.leg
                            this.first = data.first
                            this.gameCt = data.currentTeam

                            this.team[0].player = data.teamDetail.homeTeamPlayer
                            this.team[1].player = data.teamDetail.awayTeamPlayer

                            this.team[0].row = this.team[1].row = data.teamDetail.currentPlayerRow

                            this.changShowModel(this.gameCt)
                            /*if (this.team[0].pi !== 0 && this.team[1].pi !== 0) {
                                if (this.team[0].pi !== this.team[1].pi) {
                                    this.changShowModel(data.currentTeam, 0)
                                } else {
                                    this.team[0].status[0] = true
                                    this.changShowModel(data.currentTeam, 1)
                                }
                            } else {
                                this.team.map((iteam, index) => {
                                    if (iteam.pi !== 0) {
                                        this.team[index].status[0] = true
                                    }
                                })
                                this.changShowModel(data.currentTeam, 1)
                            }*/
                        }, 1000)
                    }
                }
            }
            ws.onclose = () => {
                console.log('close connection')
                //this.webSocket()
            }
        }
    }
}

</script>
