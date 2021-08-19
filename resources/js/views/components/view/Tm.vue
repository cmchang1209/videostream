<template>
    <div class="view">
        <div v-if="show" class="view-tm">
            <f-header :name="name" />
            <div v-for="(iteam, index) in pi" :key="index" v-show="iteam.status" class="video-area">
                <div class="video-2">
                    <f-player :id="iteam.id" :usb="2" />
                </div>
                <div class="video-4">
                    <f-player :id="iteam.id" :usb="4" />
                </div>
                <div class="video-1">
                    <f-player :id="iteam.id" :usb="1" />
                </div>
            </div>
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
import Player from './Player.vue'
export default {
    components: {
        'f-player': Player,
        'f-header': Header
    },
    props: ['id', 'match'],
    data() {
        return {
            name: '',
            data: [],
            show: false,
            showErrorMsg: false,
            pi: [
                { id: 7, status: false },
                { id: 13, status: true }
            ]
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
                    }
                }).catch(error => {
                    console.log(error)
                })
        }
    }
}

</script>
