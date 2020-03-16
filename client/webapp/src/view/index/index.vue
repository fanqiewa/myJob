<template>
    <div>
        首页
    </div>
</template>

<script>
export default {
    name:"",
    components: {},
    props: {},
    data() {
        return {
        }
    },
    created() {

    },
    methods: {

    }
}
</script>

<style scoped>
.nav {
    height: 50px;
    width: 100%;
    background: #2e3849;
    border-bottom: 2px solid #478cc6;
    font-size: 14px;
}

.nav__container {
    width: 1100px;
    height: 50px;
    margin: 0 auto;
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
</style>
