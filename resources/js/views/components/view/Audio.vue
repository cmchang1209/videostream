<template>
    <div>
    </div>
</template>
<script>
export default {
    components: {},
    data() {
        return {}
    },
    created() {},
    mounted() {
        let url = 'ws://videostream.fidodarts.com:8082/p5-1'
        let player = new JSMpeg.Player(url, {
            autoplay: true,
            pauseWhenHidden: false,
            onAudioDecode(decoder, time) {
                //console.log(time)
            }
        })
    },
    methods: {}
}

</script>
