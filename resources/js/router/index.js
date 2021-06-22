import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Home from '../views/Home'
import Login from '../views/Login'
import Tournament from '../views/Tournament'
import League from '../views/League'

import TournamentList from '../views/components/tournament/List'
import AddTournament from '../views/components/tournament/Add'
import TournamentLive from '../views/components/tournament/Live'

import LeagueLive from '../views/components/league/Live'

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
            name: 'Home',
            component: Home,
            beforeEnter(to, from, next) {
                if (sessionStorage.getItem('id') === null) {
                    next('/login')
                } else {
                    next()
                }
            }
        },
        {
            path: '/login',
            name: 'Login',
            component: Login,
            beforeEnter(to, from, next) {
                if (sessionStorage.getItem('id') !== null) {
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
                if (sessionStorage.getItem('id') === null) {
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
