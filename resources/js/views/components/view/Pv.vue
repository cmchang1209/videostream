<template>
    <div class="no-user-select pv">
        <h3>{{ e_name }}</h3>
        <el-row :gutter="20">
            <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8" class="video video-1">
                <f-player :id="id" :usb="1" />
            </el-col>
            <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8" class="video">
                <f-player :id="id" :usb="2" />
            </el-col>
            <el-col :xs="24" :sm="24" :md="8" :lg="8" :xl="8" class="video">
                <f-player :id="id" :usb="4" />
            </el-col>
        </el-row>
        <iframe allow="autoplay" :src="audioSrc" style="display: none;"></iframe>
    </div>
</template>
<script>
import Player from './Player.vue'
export default {
    components: {
        'f-player': Player,
    },
    props: ['id'],
    data() {
        return {
            e_name: ''
        }
    },
    created() {
        this.fetchData()
    },
    computed: {
        audioSrc() {
            let pi = 0
            return `http://${document.location.hostname}/view/audio?id=${this.id}`
        }
    },
    mounted() {
        document.body.style.overflow = 'auto'
    },
    methods: {
        fetchData() {
            if (typeof this.id === 'undefined') {
                this.showErrorMsg = true
                return false
            }
            axios
                .get('/api/getEquipmentPort', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.e_name = data.data[0].ename
                        this.runFFmpeg()
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        runFFmpeg() {
            this.$socket.client.emit('runFFmpeg', { id: this.id, usb: 2 })
            setTimeout(() => {
                this.$socket.client.emit('runFFmpeg', { id: this.id, usb: 4 })
                setTimeout(() => {
                    this.$socket.client.emit('runFFmpeg', { id: this.id, usb: 1 })
                }, 10000)
            }, 10000)
        }
    }
}

</script>
<style scoped lang="scss">
.pv {
    /* background-color: #252525; */

    h3 {
        text-align: center;
        margin: 2rem 0 2rem 0;
    }

    .video {
        position: relative;
        height: calc(33vw * 720 / 960);
        margin-bottom: 15px;
    }
}

@media only screen and (max-width: 767px) {
    .video {
        height: calc(100vw * 720 / 960) !important;
    }
}

</style>
<style scoped>
.video>>>canvas {
    position: absolute;
    width: 100%;
}

.video>>>.player {
    display: flex;
    height: 100%;
    position: absolute;
    width: calc(100% - 20px);
    align-items: center;
}

</style>
