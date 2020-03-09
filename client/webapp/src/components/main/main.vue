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
                    <div class="login" @click="goLogin">注册/登录</div>
                </div>
            </div>
        </div>
        <div>
            <router-view/>
        </div>
    </div>
</template>

<script>
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
            selectNav: 1
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
        }
    }
}
</script>

<style scoped>
    @import 'main.css';
</style>
