<template>
    <div>
    </div>
</template>
<script>
export default {
    components: {},
    props: ['id'],
    data() {
        return {
            player: null
        }
    },
    created() {},
    mounted() {
        if (typeof this.id === 'undefined' || this.id === 0) {

        } else {
            let url = `ws://videostream.fidodarts.com:8082/p${this.id}-1`
            if (this.player !== null) {
                this.player.destroy()
                this.player = new JSMpeg.Player(url, {
                    autoplay: true,
                    pauseWhenHidden: false,
                    onAudioDecode(decoder, time) {
                        //console.log(time)
                    }
                })
            } else {
                this.player = new JSMpeg.Player(url, {
                    autoplay: true,
                    pauseWhenHidden: false,
                    onAudioDecode(decoder, time) {
                        //console.log(time)
                    }
                })
            }
        }
    },
    methods: {}
}

</script>
