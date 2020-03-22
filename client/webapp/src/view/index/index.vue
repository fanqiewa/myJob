<template>
    <div>
        <div class="container">
            <div class="search">
                <div class="search__type">
                    <span class="type">职位类型类类类</span>
                    <div class="type-icon">
                        <Icon type="ios-arrow-down" />
                    </div>
                </div>
                <input class="search__input" type="text" name="" id="">
            </div>
           <div class="search-btn">搜索</div>
        </div>
    </div>
</template>

<script>
import util from '@/libs/util'
import Cookies from 'js-cookie'
import { prefixApi } from "@/utils/env"
export default {
    name:"",
    components: {},
    props: {},
    data() {
        return {
            positionAllList: [],
        }
    },
    created() {
        this.getAllPositionList()
    },
    methods: {

        /** -------------------------------获取数据------------------------------------- */

        /**
         * 获取职位列表
         */
        getAllPositionList() {
            const self = this
            self.$axios.post(prefixApi + '/getAllPositionList').then(({data}) => {
                if (data.code == 200) {
                    self.positionAllList = data.obj.list
                } 
            })
        },

    }
}
</script>

<style scoped>
.container {
    width: 900px;
    margin: 30px auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.search {
    display: flex;
    align-items: center;
    box-shadow: 0 0 6px 0 rgba(0,0,0,.13);
    border: 1px solid #ddd;
    background: #fff;
    box-sizing: border-box;
}
.search:hover {
    border: 1px solid #478cc6;
}
.search__type {
    display: flex;
    align-items: center;
    box-sizing: border-box;
    padding: 0 20px;
    font-size: 16px;
    color: #61687c;
    cursor: pointer;
}
.type {
    width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.type-icon {
    width: 20px;
}
.search__input {
    font-size: 16px;
    border: 0;
    color: #414a60;
    outline: none;
    width: 600px;
    padding: 20px;
}
.search-btn {
    background: #478cc6;   
    color: #fff;
    padding: 20px;
    font-size: 16px;
    width: 140px;
    text-align: center;
    cursor: pointer;
}
</style>
