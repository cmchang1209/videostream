<template>
    <div>
    </div>
</template>
<script>
export default {
    components: {},
    props: ['id'],
    data() {
        return {}
    },
    created() {},
    mounted() {
        if (typeof this.id === 'undefined' || this.id === 0) return false
        let url = `ws://videostream.fidodarts.com:8082/p${this.id}-1`
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
