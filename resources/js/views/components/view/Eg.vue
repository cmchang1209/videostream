<template>
    <div class="view-eg" v-loading="video_loading">
        <template v-if="id !== ''">
            <h3 style="text-align: center;">{{ eName }}</h3>
            <el-row style="margin: 1.25rem 0;">
                <el-col :span="24" style="text-align: center;">
                    <span>頻道：</span>
                    <el-radio-group v-model="radio">
                        <el-radio :disabled="playstatus" :label="1">機台</el-radio>
                        <el-radio :disabled="playstatus" :label="2">靶</el-radio>
                        <el-radio :disabled="playstatus" :label="4">玩家</el-radio>
                    </el-radio-group>
                </el-col>
            </el-row>
            <el-row style="margin: 1.25rem 0;">
                <el-col :span="24" style="text-align: center;">
                    <el-button type="success" round @click.native.prevent="handlePlay">播放</el-button>
                    <el-button type="danger" round @click.native.prevent="handleStop">停止</el-button>
                </el-col>
            </el-row>
            <div id="video-player" class="player eg">
                <h3 v-if="!playstatus" style="text-align: center;">
                    請選擇頻道後，執行播放
                </h3>
            </div>
            <div v-if="playstatus && !video_loading && radio === 2" class="block">
                <el-form :model="form">
                    <el-form-item label="自動曝光" style="margin-bottom: 0">
                        <el-switch v-model="form.exposure_auto" active-text="on" inactive-text="off" @change="changeexposureAuto">
                        </el-switch>
                    </el-form-item>
                    <p style="margin-top: 0; font-size: .75rem;">(自定義曝光值需要先關閉自動曝光)</p>
                </el-form>
                <el-form :model="form" label-position="top">
                    <el-form-item v-if="!form.disabled" :label="'設定曝光值 ('+ exposure + ')'" style="margin-bottom: 5rem;">
                        <el-slider v-model="form.exposure_absolute.k" :min="0" :max="2000" :step="100" show-stops :disabled="form.disabled">
                        </el-slider>
                        <el-slider v-model="form.exposure_absolute.b" :min="0" :max="b_max" :step="10" show-stops :disabled="form.disabled">
                        </el-slider>
                        <el-slider v-model="form.exposure_absolute.g" :min="g_min" :max="g_max" :step="1" show-stops :disabled="form.disabled">
                        </el-slider>
                        <br>
                        <el-button type="primary" plain :disabled="form.disabled" @click="setV4l2ExposureAbsolute" :loading="loading">確定</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </template>
        <template v-else>
            <h1 style="text-align: center;">NOT FOUND PI DATA</h1>
            <p style="text-align: center;">請聯絡 FIDODARTS 管理員</p>
        </template>
    </div>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    components: {},
    props: ['params'],
    sockets: {
        connect() {},
        echoV4l2Value(val) {
            if (val === 'error') {
                this.$message({
                    message: '設定錯誤，請稍後再試',
                    type: 'error',
                    offset: 90
                })
            } else {
                var v = val.split('\n')
                var auto = v[0].split(':')
                this.form.exposure_auto = this.form.disabled = auto[1] * 1 === 3 ? true : false
                var exposure = v[1].split(':')
                this.exposure = exposure[1] * 1
                this.form.exposure_absolute.k = Math.floor(this.exposure / 100) * 100
                this.form.exposure_absolute.b = Math.floor((this.exposure - this.form.exposure_absolute.k) / 10) * 10
                this.form.exposure_absolute.g = this.exposure - this.form.exposure_absolute.k - this.form.exposure_absolute.b
                this.loading = false
            }
        },
        echoV4l2ExposureAuto(val) {
            if (val === 'error') {
                this.$message({
                    message: '設定錯誤，請稍後再試',
                    type: 'error',
                    offset: 90
                })
            }
        },
        echoV4l2ExposureAbsolute(val) {
            if (val === 'error') {
                this.$message({
                    message: '設定錯誤，請稍後再試',
                    type: 'error',
                    offset: 90
                })
                this.loading = false
            }
        }
    },
    data() {
        return {
            radio: null,
            playstatus: false,
            wk: null,
            player: null,
            url: '',
            video: null,
            canvas: null,
            oc: null,
            playRadio: null,
            form: {
                exposure_auto: true,
                disabled: true,
                exposure_absolute: {
                    k: 0,
                    b: 0,
                    g: 0
                }
            },
            loading: false,
            exposure: 0,
            video_loading: false,
            audioPlayer: null,
            showAudioBt: false,
            e_name: '',
            id: ''
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
                    case 'stalled':
                        this.stalled()
                        break
                }
            }
        }
        this.fetchData()
    },
    computed: {
        ...mapGetters(['isIosDevice']),
        b_max() {
            return this.form.exposure_absolute.k === 2000 ? 40 : 90
        },
        g_min() {
            switch (true) {
                case (this.form.exposure_absolute.k === 0 && this.form.exposure_absolute.b === 0):
                    return 3
                    break
                default:
                    return 0
            }
        },
        g_max() {
            switch (true) {
                case (this.form.exposure_absolute.k === 2000 && this.form.exposure_absolute.b === 40):
                    return 7
                    break
                default:
                    return 9
            }
        },
        eName() {
            return this.e_name
        }
    },
    mounted() {
        this.video = document.getElementById(`video-player`)
    },
    methods: {
        fetchData() {
            if (this.params) {
                try {
                    let id = Base64.decode(this.params)
                    id = id.split('-')
                    if(id[0] === 'fidodartsVideoLive') {
                        this.id = id[1]
                    }
                } catch (e) {
                    return
                }

            }
            axios
                .get('/api/getEquipmentPort', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.e_name = data.data[0].ename
                    }
                }).catch(error => {
                    console.log(error)
                })
        },
        handlePlay() {
            let urlData = this.$store.state.gobalData.server
            if (this.radio) {
                if (this.playRadio === null || this.playRadio !== this.radio) {
                    if (this.radio === 1) {
                        let audioUrl = `ws://${urlData}:8084/p${this.id}-${this.radio}`
                        this.audioPlayer = new JSMpeg.Player(audioUrl, {
                            autoplay: true,
                            pauseWhenHidden: false,
                            onPlay: player => {
                                setTimeout(() => {
                                    this.showAudioBt = true
                                    player.audioOut.unlock(this.onUnlocked)
                                }, 1000)
                            }
                        })

                    }
                    this.playstatus = true
                    this.video_loading = true
                    this.playRadio = this.radio
                    this.url = `ws://${urlData}:8082/p${this.id}-${this.radio}`
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
                        setTimeout(() => {
                            this.player = new JSMpeg.Player(this.url, {
                                canvas: this.canvas,
                                audio: false,
                                pauseWhenHidden: false,
                                onPlay: source => {
                                    this.play()
                                }
                            })
                        }, 1000)
                    }
                }
                this.$socket.client.emit('runFFmpeg', { id: this.id * 1, usb: this.radio })
            }
        },
        handleStop() {
            if (this.playRadio !== null) {
                if (this.audioPlayer !== null) {
                    this.audioPlayer.destroy()
                    this.audioPlayer = null
                }
                if (!this.isIosDevice) {
                    this.wk.postMessage({ type: 'destroy' })
                } else {
                    this.player.destroy()
                    this.player = null
                    this.destroy()
                }
                this.$socket.client.emit('stopFFmpeg', { id: this.id * 1, usb: this.radio })
            } else {
                /* stop all if usb = 0 */
                this.$socket.client.emit('stopFFmpeg', { id: this.id * 1, usb: 0 })
            }
        },
        play() {
            if (this.radio === 2) {
                //this.$socket.client.emit('getV4l2', { id: this.id * 1, usb: this.radio })
            }
            setTimeout(() => {
                this.video_loading = false
                this.playstatus = true
            }, 5000)
        },
        destroy() {
            this.playstatus = false
            if (this.canvas !== null) {
                this.url = ''
                this.canvas.remove()
                this.canvas = null
                this.oc = null
                this.playRadio = null
            }
        },
        changeexposureAuto() {
            this.$socket.client.emit('setV4l2ExposureAuto', { id: this.id, value: this.form.exposure_auto })
        },
        setV4l2ExposureAbsolute() {
            let value = this.form.exposure_absolute.k + this.form.exposure_absolute.b + this.form.exposure_absolute.g
            this.$socket.client.emit('setV4l2ExposureAbsolute', { id: this.id, value: value })
            this.loading = true
        },
        onUnlocked() {
            this.audioPlayer.volume = 1
        },
        handleAudio() {
            this.audioPlayer.audioOut.unlock(this.onUnlocked)
        }
    }
}

</script>
<style scoped lang="scss">
.view-eg {
    height: 100vh;
    overflow: auto;
}

.block {
    width: 960px;
    max-width: 90%;
    margin: .25rem auto;
}

</style>
