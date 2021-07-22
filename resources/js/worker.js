importScripts('./jsmpeg.min.js')
this.window = this
self.addEventListener('message', (evt) => {
    const data = evt.data
    switch (data.type) {
        case 'create':
            const { url, canvas, ...config } = data.data
            this.player = new JSMpeg.Player(url, {
                canvas,
                //disableGl: true,
                //disableWebAssembly: true,
                //pauseWhenHidden: false,
                //videoBufferSize: 10 * 1024 * 1024,
                //videoBufferSize: 1024,
                ...config,
                onPlay: function(source) {
                    self.postMessage({ type: 'play' })
                }
            })
            break
        case 'destroy':
            this.player.destroy()
            self.postMessage({ type: 'destroy' })
            break
    }
})
