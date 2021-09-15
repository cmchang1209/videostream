export default {
    state: {
        isGuest: true,
        me: {},
        appLoading: false,
        drawer: false,
        isView: false,
        wsTest2: {
            ip: '52.199.5.88',
            port: 4649
        },
        wsTest1: {
            ip: '52.199.5.88',
            port: 4650
        },
        ws: {
            ip: '18.183.140.103',
            port: 4649
        },
        server: '52.196.115.119'
    },
    getters: {
        isTouchDevice(state) {
            return ('ontouchstart' in window || navigator.msMaxTouchPoints) || false
        },
        isIosDevice(state) {
            //console.log(navigator.userAgent)
            /*var u = navigator.userAgent
            return !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)*/
            return /(Mac|iPhone|iPod|iPad)/i.test(navigator.platform) && !window.MSStream
        }
    },
    mutations: {
        setMe(state, val) {
            state.me = val
        },
        changeLoginStatus(state, val) {
            state.isGuest = val
        },
        changeAppLoadingStatus(state, val) {
            state.appLoading = val
        },
        changeDrawerStatus(state, val) {
            state.drawer = val
        },
        changePageStatus(state, val) {
            state.isView = val
        }
    },
    actions: {
        setMe: ({ commit }, val) => {
            commit('setMe', val)
        },
        changeTitle: ({ commit }, val) => {
            document.title = `${val}::`;
        },
        changeLoginStatus: ({ commit }, val) => {
            commit('changeLoginStatus', val)
        },
        changeAppLoadingStatus: ({ commit }, val) => {
            commit('changeAppLoadingStatus', val)
        },
        changeDrawerStatus: ({ commit }, val) => {
            commit('changeDrawerStatus', val)
        },
        changePageStatus: ({ commit }, val) => {
            commit('changePageStatus', val)
        }
    }
}
