<template>
    <div>
        <div class="container">
            <div class="container__top" :style="{height: topHeight + 'px'}"></div>
            <div class="login-wrapper">
                <div class="login__company" @click="goHome">
                    <img class="logo" src="../../assets/images/index/logo.png" alt="">
                    <div class="company__name">无聊</div>
                    <div class="company__detail">招聘</div>
                </div>
                <div class="login__box" @keydown.enter="loginOrRegister">
                    <div class="login__name">{{isRegister ? '登录' : '注册'}}</div>
                    <div class="login__account">
                        <img src="../../assets/images/login/user.png" alt="" class="user-img">
                        <input type="text" class="input" placeholder="请输入账号" v-model="account">
                    </div>
                    <div class="login__password">
                        <img src="../../assets/images/login/lock.png" alt="" class="user-img">
                        <input type="password" class="input" placeholder="请输入登录密码" v-model="password">
                    </div>
                    <div class="login__password" v-if="!isRegister">
                        <img src="../../assets/images/login/lock.png" alt="" class="user-img">
                        <input type="password" class="input" placeholder="请确认登录密码" v-model="confirmPassword">
                    </div>
                    <div class="btn" @click="loginOrRegister">
                        {{isRegister ? '登录' : '注册'}}
                        <Spin fix v-if="!isSubmit">
                            <Icon type="ios-loading" size=18 class="demo-spin-icon-load"></Icon>
                        </Spin>
                    </div>
                    
                    <div class="no-account">
                        <span class="no-account__tip">{{isRegister ? '还没有账号？' : '已有无聊账号？'}}</span>
                        <span class="register" @click="toggleLogin">{{isRegister ? '去注册' : '去登录'}}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- <input type="file" name="filedata" accept="image/jpeg,image/jpg,image/png" id='imageInput' @change="loadImagesFile($event)" ref="avatarInput"/>
        <img :src="avatar" class="img-avatar" v-if="avatar"> -->
    </div>
</template>

<script>
import { prefixApi } from "@/utils/env"
export default {
    name:"",
    components: {},
    props: {},
    data() {
        return {
            isSubmit: true, // 防止多次提交
            isRegister: true,
            avatar: '',
            topHeight: document.documentElement.clientHeight / 2,
            account: '',
            password: '',
            confirmPassword: '',
        }
    },
    created() {

    },
    methods: {
        
        /**
         * 回到首页
         */
        goHome() {
            this.$router.push({
                name: 'home'
            })
        },

        /**
         * 去注册/去登录
         */
        toggleLogin() {
            this.isRegister = !this.isRegister
        },

        /**
         * 注册/登录
         */
        loginOrRegister() {
            const { isRegister, account, password, confirmPassword } = this
            if (!account) {
                this.$Message.error("请输入账号")
                return
            }
            if (!password) {
                this.$Message.error("请输入密码")
                return
            }
            if (isRegister) {
                this.login()
                return
            }
            // 注册
            if (!confirmPassword) {
                this.$Message.error("请输入确认登录密码")
                return
            }
            // 注册
            if (password != confirmPassword) {
                this.$Message.error("2次密码不一致")
                return
            }
            this.register()
        },

        /**
         * 登录
         */
        login() {
            const self = this
            if (!self.isSubmit) {
                return
            }
            self.isSubmit = false
            self.$axios.post(prefixApi + "/login", {account: self.account, password: self.password}).then(({data}) => {
                if (data.code == 200) {
                    self.$Notice.success({
                        title: "提示",
                        desc: "登录成功， 2秒后自动跳转至首页",
                        duration: 2
                    })
                    setTimeout(() => {
                        self.$router.push({
                            name: 'home'
                        })
                    }, 2000)
                } else if (data.code == 500) {
                    self.$Notice.warning({
                        title: "提示",
                        desc: data.msg
                    })
                }
                self.isSubmit = true
            })
        },

        /**
         * 注册
         */
        register() {
            const self = this
            if (!self.isSubmit) {
                return
            }
            self.isSubmit = false
            self.$axios.post(prefixApi + "/register", {account: self.account, password: self.password}).then(({data}) => {
                if (data.code == 200) {
                    self.$Notice.success({
                        title: "提示",
                        desc: "注册成功， 2秒后自动跳转至首页",
                        duration: 2
                    })
                    setTimeout(() => {
                        self.$router.push({
                            name: 'home'
                        })
                    }, 2000)
                } else if (data.code == 500) {
                    self.$Notice.warning({
                        title: "提示",
                        desc: data.msg
                    })
                }
                self.isSubmit = true
            })
        },

        /**
         * 加载图片
         */
        loadImagesFile(e) {
            let file = e.target.files[0]
            let reader = new FileReader()
            let that = this
            reader.readAsDataURL(file)
            reader.onload = (e) => {
                that.avatar = e.target.result
            }
        }
    }
}
</script>

<style scoped>
.img-avatar {
    width: 100px;
    height: 100px;
}

.container__top {
    background: #478cc6;
}   

.login-wrapper {
    position: absolute;
    top: 50%;
    left: 50%;
    transform:translate(-50%,-50%);
    width: 420px;
}

.logo {
    margin-right: 20px;
    width: 50px;
    height: 50px;
}

.login__company {
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}


.company__name {
    color: #fff;
    font-size: 26px;
    font-weight: 600;
    margin-right: 20px;
}

.company__detail {
    color: #fff;
    font-size: 22px;
    font-weight: 600;
    position: relative;
    padding-left: 20px;
}

.company__detail::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 2px;
    height: 20px;
    background: #fff;
}

.login__box {
    border-radius: 4px;
    background: #fff;
    margin-top: 20px;
    width: 100%;
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, .08);
}

.login__name {
    color: #1f2d3d;
    font-size: 20px;
    border-bottom: 1px solid #e0e6ed;
    text-align: center;
    padding: 20px;
}

.login__account, .login__password {
    margin-top: 40px;
    display: flex;
    justify-content: center;
    position: relative;
}

.input {
    width: 350px;
    height: 22px;
    color: #1f2d3d;
    font-size: 14px;
    line-height: 22px;
    padding: 20px 16px;
    outline: none;
    border: 1px solid #e0e6ed;
    padding-left: 50px;
}

.user-img {
    position: absolute;
    top: 50%;
    left: 40px;
    transform: translateY(-50%);
    width: 30px;
    height: 30px;
}

.btn {
    background: #478cc6;
    width: 240px;
    height: 40px;
    color: #fff;
    line-height: 40px;
    font-size: 18px;
    border-radius: 2px;
    margin: 40px auto;
    text-align: center;
    cursor: pointer;
    position: relative;
}

.no-account {
    text-align: center;
    font-size: 14px;
    padding-bottom: 20px;
}

.no-account__tip {
    color: #a3b3c7;
}

.register {
    color: #478cc6;
    cursor: pointer;
}

.register:hover {
    text-decoration: underline;
}

.demo-spin-icon-load{
    animation: ani-demo-spin 1s linear infinite;
}
@keyframes ani-demo-spin {
    from { transform: rotate(0deg);}
    50%  { transform: rotate(180deg);}
    to   { transform: rotate(360deg);}
}
</style>
