<template>
    <div>
        <div class="nav">
            <div class="nav__container">
                <div class="nav__left">
                    <img class="logo" src="../../assets/images/index/logo.png" alt="">
                    <div class="nav__city">南宁</div>
                    <div class="nav__route">
                        <div :class="{'home': true, 'active': selectNav == item.id}" v-for="item in navList" :key="item.id">{{item.name}}</div>
                    </div>
                </div>
                <div class="nav__right">
                    <div class="recruit">我要招聘</div>
                    <div class="login" @click="goLogin" v-if="!userAvatar">注册/登录</div>
                    <div v-else class="userinfo">
                        <img class="userinfo__avatar" :src="getHeadimgurl" alt="">
                        
                        
                        <Dropdown>
                            <a href="javascript:void(0)">
                                <span class="userinfo__name" >{{userName}}</span>
                            </a>
                            <DropdownMenu slot="list">
                                <DropdownItem @click.native="goUserInfo">
                                    <div>个人信息</div>
                                    <div class="edit">编辑在线简历</div>
                                </DropdownItem>
                                <DropdownItem>
                                    <div>账号设置</div>
                                    <div class="edit">修改用户头像、密码、名称</div>
                                </DropdownItem>
                                <DropdownItem>退出登录</DropdownItem>
                            </DropdownMenu>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <router-view/>
        </div>
    </div>
</template>

<script>
import Cookies from 'js-cookie'
export default {
    name:"",
    components: {},
    props: {},
    data() {
        return {
            navList: [
                {
                    id: 1,
                    name: '首页',
                    url: '/'
                },
                {
                    id: 2,
                    name: '全职招聘',
                    url: '/'
                },
                {
                    id: 3,
                    name: '兼职招聘',
                    url: '/'
                },
                {
                    id: 4,
                    name: '企业服务',
                    url: '/'
                },
                {
                    id: 5,
                    name: '关于我们',
                    url: '/'
                }
            ],
            selectNav: 1,
        }
    },
    computed: {
        userAvatar () {
            return Cookies.get('headimgurl')
        },
        userName () {
            return Cookies.get('name')
        },
        getHeadimgurl () {
            return this.IMG_BASE_URL + this.userAvatar
        }
    },
    created() {
    },
    methods: {

        /**
         * 注册/登录        
         */
        goLogin() {
            this.$router.push({
                name: 'login'
            })
        },

        /**
         * 路由跳转-个人信息 
         */
        goUserInfo() {
            this.selectNav = ''
            this.$router.push({
                name: 'userInfo'
            })
        }
    }
}
</script>

<style scoped>
    @import 'main.css';
</style>
