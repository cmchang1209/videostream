import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Home from '../views/Home'
import Login from '../views/Login'
import Tournament from '../views/Tournament'
import League from '../views/League'

import EquipmentList from '../views/components/equipment/List'
import AddEquipment from '../views/components/equipment/Add'
import EditEquipment from '../views/components/equipment/Edit'
import PortEquipment from '../views/components/equipment/Port'

import TournamentList from '../views/components/tournament/List'
import AddTournament from '../views/components/tournament/Add'
import EditTournament from '../views/components/tournament/Edit'
import TournamentLive from '../views/components/tournament/Live'

import LeagueLive from '../views/components/league/Live'


import T3 from '../views/components/view/T3'

//不允許導航到當前位置
const originalPush = VueRouter.prototype.push
VueRouter.prototype.push = function push(location, onResolve, onReject) {
    if (onResolve || onReject) return originalPush.call(this, location, onResolve, onReject);
    return originalPush.call(this, location).catch(err => err)
}


export default new VueRouter({
    mode: 'history',
    routes: [{
            path: '/',
            //name: 'Home',
            component: Home,
            beforeEnter(to, from, next) {
                if (localStorage.getItem('id') === null) {
                    next('/login')
                } else {
                    next()
                }
            },
            children: [{
                path: '',
                name: 'EquipmentList',
                component: EquipmentList
            }, {
                path: 'add',
                name: 'AddEquipment',
                component: AddEquipment,
                beforeEnter(to, from, next) {
                    if (localStorage.getItem('id') === null) {
                        next('/')
                    } else {
                        var id = Base64.decode(localStorage.getItem('id'))
                        var ids = id.split(',')
                        if (ids[2] * 1 === 1) {
                            next()
                        } else {
                            next('/')
                        }
                    }
                }
            }, {
                path: 'edit',
                name: 'EditEquipment',
                component: EditEquipment,
                props: (route) => ({ id: route.query.id }),
                beforeEnter(to, from, next) {
                    if (localStorage.getItem('id') === null) {
                        next('/')
                    } else {
                        var id = Base64.decode(localStorage.getItem('id'))
                        var ids = id.split(',')
                        if (ids[2] * 1 === 1) {
                            next()
                        } else {
                            next('/')
                        }
                    }
                }
            }, {
                path: 'port',
                name: 'PortEquipment',
                component: PortEquipment,
                props: (route) => ({ id: route.query.id }),
                beforeEnter(to, from, next) {
                    axios
                        .get('/api/getEquipmentStatus', {
                            params: { id: to.query.id }
                        })
                        .then(response => {
                            let data = response.data.data
                            if (data.data) {
                                next()
                            } else {
                                next('/')
                            }
                        })
                        .catch(error => {
                            next('/')
                        })
                }
            }]
        },
        {
            path: '/login',
            name: 'Login',
            component: Login,
            beforeEnter(to, from, next) {
                if (localStorage.getItem('id') !== null) {
                    next('/')
                } else {
                    next()
                }
            }
        },
        {
            path: '/tournament',
            component: Tournament,
            beforeEnter(to, from, next) {
                if (localStorage.getItem('id') === null) {
                    next('/login')
                } else {
                    next()
                }
            },
            children: [{
                    path: 'list',
                    name: 'TournamentList',
                    component: TournamentList
                },
                {
                    path: 'add',
                    name: 'AddTournament',
                    component: AddTournament
                },
                {
                    path: 'edit',
                    name: 'EditTournament',
                    component: EditTournament,
                    props: (route) => ({ id: route.query.id })
                },
                {
                    path: 'live',
                    name: 'TournamentLive',
                    component: TournamentLive
                }
            ]
        },
        {
            path: '/league',
            component: League,
            children: [{
                path: 'live',
                name: 'LeagueLive',
                component: LeagueLive
            }]
        },
        {
            path: "*",
            redirect: "/"
        }
    ],
    /*
    切換路由scroll top 回頂部
     */
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
})
