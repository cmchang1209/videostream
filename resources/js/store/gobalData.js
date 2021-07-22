export default {
    state: {
        isGuest: true,
        me: {},
        appLoading: false,
        drawer: false,
        isView: false
    },
    getters: {
        isTouchDevice(state) {
            return ('ontouchstart' in window || navigator.msMaxTouchPoints) || false
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
