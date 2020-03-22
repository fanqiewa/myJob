<template>
    <div>
        <div class="nav">
            <div class="nav__container">
                <div class="nav__left">
                    <img class="logo" src="../../assets/images/index/logo.png" alt="">
                    <div class="nav__city">南宁</div>
                    <div class="nav__route">
                        <div :class="{'home': true, 'active': selectNav == item.id}" v-for="(item, index) in navList" :key="item.id" @click="changeRouter(index)">{{item.name}}</div>
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
                    url: 'home'
                },
                {
                    id: 2,
                    name: '全职招聘',
                    url: 'home'
                },
                {
                    id: 3,
                    name: '兼职招聘',
                    url: 'home'
                },
                {
                    id: 4,
                    name: '企业服务',
                    url: 'home'
                },
                {
                    id: 5,
                    name: '关于我们',
                    url: 'home'
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
        },

        /**
         * 路由跳转
         */
        changeRouter(index) {
            const navList = this.navList
            this.selectNav = navList[index].id
            this.$router.push({
                name: navList[index].url
            })
        }
    }
}
</script>

<style scoped>
.nav {
    height: 60px;
    width: 100%;
    background: #2e3849;
    border-bottom: 2px solid #478cc6;
    font-size: 14px;
}

.nav__container {
    width: 1200px;
    height: 60px;
    margin: 0 auto;
    background: #2e3849;
    border-bottom: 2px solid #478cc6;
}

.nav__container {
    display: flex;
    justify-content: space-between;
    color: #fff;
    align-items: center;
}

.logo {
    width: 30px;
}

.nav__city {
    margin: 0 20px;
    color: #07ada2;
}

.nav__left {
    display: flex;
    align-items: center;
}

.nav__route {
    display: flex;
}

.nav__route > div {
    position: relative;
    padding: 0 20px;
    cursor: pointer;
    box-sizing: border-box;
}

.nav__route > div:not(:first-child)::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 2px;
    height: 10px;
    background: #fff;
}

.active {
    color: #478cc6;
    font-weight: 600;
}

.nav__right {
    display: flex;
    align-items: center;
}

.recruit {
    position: relative;
    margin: 0 20px;
    border: 1px solid #07ada2;
    padding: 2px 4px;
    cursor: pointer;
    border-radius: 2px;
    overflow: hidden;
}

.recruit:hover:before {
    animation: recruit .2s linear forwards;
}

.recruit::before {
    content: "我要招聘";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: #07ada2;
    border: 1px solid #07ada2;
    text-align: center;
    line-height: 20px;
}
@keyframes recruit
{
    to {
        left: 0;
    }
}

.login:hover {
    text-decoration: underline;
    cursor: pointer;
    color: #478cc6;
}

.userinfo {
    display: flex;
    align-items: center;
}

.userinfo__avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
}

.userinfo__name {
    margin-left: 10px;
    color: #478cc6;
    cursor: pointer;
}

.userinfo__name:hover {
    text-decoration: underline;
}

.edit {
    color: #bdc1cb;
    font-size: 12px;
    margin-top: 5px;
}
</style>
