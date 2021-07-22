<template>
    <div>
        <div id="video-player" class="player"></div>
    </div>
</template>
<script>
export default {
    components: {},
    data() {
        return {
            wk: null
        }
    },
    created() {
        if (window.Worker) {
            this.wk = new Worker('../js/worker/worker.js')
        }
    },
    mounted() {
        let url = 'ws://videostream.fidodarts.com:8082/p5-1'
        let video = document.getElementById(`video-player`)
        let canvas = document.createElement("CANVAS")
        video.appendChild(canvas)
        if (this.wk !== null) {
            let oc = canvas.transferControlToOffscreen()
            this.wk.postMessage({
                type: 'create',
                data: {
                    canvas: oc,
                    url: url
                }
            }, [oc])
        }
    },
    methods: {}
}

</script>
