<template>
    <div>
        <div :id="'video-player-'+index+'-'+id+'-'+usb" class="player"></div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    components: {},
    props: ['id', 'usb', 'index'],
    data() {
        return {
            wk: null,
            player: null,
            url: '',
            video: null,
            canvas: null,
            oc: null
        }
    },
    created() {
        if (window.Worker) {
            this.wk = new Worker('../js/worker/worker.js')
            this.wk.onmessage = (evt) => {
                const data = evt.data
                switch (data.type) {
                    case 'play':
                        this.play()
                        break
                    case 'destroy':
                        this.destroy()
                        break
                }
            }
        }
    },
    computed: {
        ...mapGetters(['isIosDevice'])
    },
    mounted() {
        let urlData = this.$store.state.gobalData.server
        this.video = document.getElementById(`video-player-${this.index}-${this.id}-${this.usb}`)
        console.log(this.video)
        this.url = `ws://${urlData}:8082/p${this.id}-${this.usb}`
        this.canvas = document.createElement("CANVAS")
        this.video.appendChild(this.canvas)
        if (!this.isIosDevice) {
            this.oc = this.canvas.transferControlToOffscreen()
            this.wk.postMessage({
                type: 'create',
                data: {
                    canvas: this.oc,
                    url: this.url
                }
            }, [this.oc])
        } else {
            this.player = new JSMpeg.Player(this.url, {
                canvas: this.canvas,
                pauseWhenHidden: false,
                onPlay: source => {
                    this.play()
                }
            })
        }
    },
    methods: {
        play() {}
    }
}

</script>
