export default {
    state: {
        isGuest: true,
        appLoading: false
    },
    getters: {
        isTouchDevice(state){
            return ('ontouchstart' in window || navigator.msMaxTouchPoints) || false
        }
    },
    mutations: {
        changeLoginStatus(state, val) {
            state.isGuest = val
        },
        changeAppLoadingStatus(state, val) {
            state.appLoading = val
        }
    },
    actions: {
    	changeTitle: ({commit}, val) => {
            document.title = `${val}::`;
        },
        changeLoginStatus: ({commit}, val) => {
            commit('changeLoginStatus', val)
        },
        changeAppLoadingStatus: ({commit}, val) => {
            commit('changeAppLoadingStatus', val)
        }
    }
}