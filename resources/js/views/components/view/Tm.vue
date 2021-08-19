<template>
    <div class="view">
        <div v-if="show" class="view-tm">
            <f-header :name="name" />
            <div v-for="(iteam, index) in pi" :key="index" v-show="iteam.status" class="video-area">
                <div class="video-2">
                    <f-player :id="iteam.id" :usb="2" />
                </div>
                <div class="video-4">
                    <div class="adr-name">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,386,59">
                            <text x="10" y="42" font-size="36" stroke="#666" stroke-width="5" fill="#666" letter-spacing="1" stroke-linecap="round" stroke-linejoin="round">
                                {{ iteam.p_name }}
                            </text>
                            <text x="10" y="42" font-size="36" fill="#fff" letter-spacing="1">
                                {{ iteam.p_name }}
                            </text>
                        </svg>
                    </div>
                    <f-player :id="iteam.id" :usb="4" />
                </div>
                <div class="video-1">
                    <f-player :id="iteam.id" :usb="1" />
                </div>
            </div>
            <f-footer />
        </div>
        <div v-else class="view-tm" style="display: flex; align-items: center; justify-content: center">
            <h1 style="color: white;" v-show="showErrorMsg">無相關賽事</h1>
        </div>
        <!-- <Player />
        <iframe allow="autoplay" :src="audioSrc" style="display: none;"></iframe> -->
    </div>
</template>
<script>
import Header from './layout/Header.vue'
import Footer from './layout/Footer.vue'
import Player from './Player.vue'
export default {
    components: {
        'f-player': Player,
        'f-header': Header,
        'f-footer': Footer
    },
    props: ['id', 'match'],
    data() {
        return {
            name: '',
            data: [],
            show: false,
            showErrorMsg: false,
            pi: []
        }
    },
    created() {
        this.fetchData()
    },
    computed: {
        audioSrc() {
            return `http://${document.location.hostname}/view/audio`
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

                            this.pi.push(d)
                        }
                        console.log(this.pi)
                    }
                }).catch(error => {
                    console.log(error)
                })
        }
    }
}

</script>
