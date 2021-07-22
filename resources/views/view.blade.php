<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/view.css') }}">
    <title></title>
</head>

<body>
    <div id="app">
        <div id="video-player" class="player"></div>
    </div>
    <script src="https://unpkg.com/vue@next"></script>
    <script src="./js/jsmpeg.js"></script>
    <script>
    if (window.Worker) {
        const worker = new Worker('./js/worker/worker.js')

        const App = {
            data() {
                return {
                    counter: 0
                }
            },
            mounted() {
                let url = 'ws://videostream.fidodarts.com:8082/p5-1'
                let video = document.getElementById(`video-player`)
                let canvas = document.createElement("CANVAS")
                video.appendChild(canvas)
                let oc = canvas.transferControlToOffscreen()
                let audioPlayer = new JSMpeg.Player(url, {
                    pauseWhenHidden: false,
                    onAudioDecode(decoder, time) {
                        console.log(time)
                    }
                })
                worker.postMessage({
                    type: 'create',
                    data: {
                        canvas: oc,
                        url: url
                    }
                }, [oc])
            },
            methods: {

            }
        }

        Vue.createApp(App).mount('#app')
    }

    </script>
</body>

</html>
