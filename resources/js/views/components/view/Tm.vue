<template>
    <div>
        <Player />
        <iframe allow="autoplay" :src="audioSrc" style="display: none;"></iframe>
    </div>
</template>
<script>
import Player from './Player.vue'
export default {
    components: { Player },
    data() {
        return {
        }
    },
    created() {},
    computed: {
        audioSrc() {
            return `http://${document.location.hostname}/view/audio`
        }
    },
    mounted() {},
    methods: {}
}

</script>
