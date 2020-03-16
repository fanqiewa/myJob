<template>
    <div>
        <div class="container">
            <div class="main">
                <!-- <Icon type="ios-create-outline" size="26" /> -->
                <div class="userinfo">
                    <div class="last-time">最后更新时间2020.03.12 00:55</div>
                    <template v-if="userInfoObj.detail">
                        <div class="name">
                            <span>{{formValidate.name}}</span>
                            <div class="edit" @click="toggleDetail">
                                <Icon type="ios-create-outline" size="22" />
                                <span>编辑</span>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail__item">
                                <Icon type="ios-create-outline" size="22" />
                                <span>1年经验</span>
                            </div>
                            <div class="detail__item">
                                <Icon type="ios-create-outline" size="22" />
                                <span>本科学历</span>
                            </div>
                            <div class="detail__item">
                                <Icon type="ios-create-outline" size="22" />
                                <span>{{jobStatusList[formValidate.job_status - 1].label}}</span>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail__item">
                                <Icon type="ios-call-outline" size="22"/>
                                <span>{{formValidate.phone}}</span>
                            </div>
                            <div class="detail__item">
                                <Icon type="ios-mail-outline" size="22"/>
                                <span>{{formValidate.email}}</span>
                            </div>
                            <div class="detail__item">
                                <Icon type="ios-send-outline" size="22"/>
                                <span>{{formValidate.wx_code}}</span>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="info">
                            <div class="info__title">编辑个人信息</div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">姓名</div>
                                    <input type="text" class="info__input" v-model="formValidate.name">
                                </div>
                                <div class="item-left">
                                    <div class="info__name">当前求职状态</div>
                                    <Select v-model="formValidate.job_status" class="info__select">
                                        <Option v-for="item in jobStatusList" :value="item.value" :key="item.value">{{ item.label }}</Option>
                                    </Select>
                                </div>
                            </div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">性别</div>
                                    <RadioGroup v-model="formValidate.sex">
                                        <Radio label="male">
                                            <span>男</span>
                                        </Radio>
                                        <Radio label="female">
                                            <span>女</span>
                                        </Radio>
                                    </RadioGroup>
                                </div>
                            </div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">生日</div>
                                    <DatePicker type="date" placeholder="请选择出生日期" style="width: 300px" v-model='formValidate.birthday'></DatePicker>
                                </div>
                                <div class="item-left">
                                    <div class="info__name">微信号（选题）</div>
                                    <input type="text" class="info__input" v-model="formValidate.wx_code">
                                </div>
                            </div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">电话</div>
                                    <input type="text" class="info__input" v-model="formValidate.phone">
                                </div>
                                <div class="item-left">
                                    <div class="info__name">邮箱（选题）</div>
                                    <input type="text" class="info__input" v-model="formValidate.email">
                                </div>
                            </div>
                            <div class="btn-box">
                                <div class="cancel" @click="toggleDetail">取消</div>
                                <div class="confirm" @click="saveUserInfo">完成</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="right">
                <div class="right__title">附件管理</div>
                <Dropdown placement="left-start" :transfer="true"> 
                    <div class="pdf__name">{{formValidate.pdf_name}}</div>
                    <DropdownMenu slot="list">
                        <DropdownItem>
                            <div>重命名</div>
                        </DropdownItem>
                        <DropdownItem>
                            <div>删除</div>
                        </DropdownItem>
                    </DropdownMenu>
                </Dropdown>
                <div class="pdf__time" v-if="formValidate.pdf_update">上传时间：{{formValidate.pdf_update}}</div>
                <div class="pdf__reload" @click="choicePdf">上传简历</div>
                <input type="file" name="upload" hidden ref="filElem" @change="getFile" accept=".pdf" single>
            </div>
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
            isSubmit: true,
            userInfoObj: {
                detail: true
            },
            jobStatusList: [
                {
                    value: 1,
                    label: '离职-随时到岗'
                },
                {
                    value: 2,
                    label: '在职-暂不考虑'
                },
                {
                    value: 3,
                    label: '在职-考虑机会'
                },
                {
                    value: 4,
                    label: '在职，月内到岗'
                }
            ],
            formValidate: {
                name: '',
                job_status: 1,
                sex: 'male',
                birthday: '',
                wx_code: '',
                phone: '',
                email: '',
                pdf_name: '',
                pdf_update: ''
            }
        }
    },
    created() {
        this.getJobHunterInfo()
    },
    methods: {
        
        /**
         * 选择pdf
         */
        choicePdf(){
            this.$refs.filElem.dispatchEvent(new MouseEvent('click')) 
        },
        
        /**
         * 改变编辑
         */
        getFile(){
            const self = this
            const inputFile = this.$refs.filElem.files[0]
            console.log(inputFile)
            if(inputFile){
                self.formValidate.pdf_name = inputFile.name
                self.formValidate.pdf_update = util.formatTimes(new Date() / 1000)
                let forms = new FormData()
                let configs = {
                    headers:{'Content-Type':'multipart/form-data'}
                }
                forms.append('file', inputFile)
                forms.append('account', Cookies.get('account'))
                forms.append('pdf_name', self.formValidate.pdf_name)
                forms.append('pdf_update', new Date() / 1000)
                self.$axios.post(prefixApi + "/uploadFile", forms, configs).then(({data}) => {
                    self.$Notice.success({
                        title: '上传成功！'
                    })
                })
            } else {
                return
            }
        },

        down() {
            window.location.href = prefixApi + "/down"
            // this.$axios.post(prefixApi + "/down", {}).then(({data}) => {
            //     console.log(data)
            // })
        },

        /**
         * 改变编辑
         */
        toggleDetail() {
            this.$set(this.userInfoObj, 'detail', !this.userInfoObj.detail)
        },

        /**
         * 获取求职值信息
         */
        getJobHunterInfo() {
            const self = this
            const account = Cookies.get('account')
            self.$axios.post(prefixApi + "/getJobHunterInfo", {account: account}).then(({data}) => {
                console.log(data)
                if (data.code == 200) {
                    let result = data.obj.result
                    data.obj.result.birthday = new Date(result.birthday)
                    data.obj.result.pdf_update = util.formatTimes(result.pdf_update)
                    self.formValidate = result
                } else if (data.code == 500) {
                    self.$Notice.warning({
                        title: '获取用户信息失败！',
                        desc: data.msg
                    })
                }
            })

        },

        /**
         * 保存
         */
        saveUserInfo() {
            
            const self = this
            const { name, job_status, sex, birthday, wx_code, phone, email } = self.formValidate
            const postData = {
                user_json: {
                    name,
                    job_status,
                    sex: sex == 'male' ? 1 : 2,
                    birthday: Date.parse(birthday) / 1000,
                    wx_code,
                    phone,
                    email
                },
                account: Cookies.get('account')
            }

            if (!self.isSubmit) {
                return
            }
            self.isSubmit = false
            self.$axios.post(prefixApi + "/updateJobHunterInfo", postData).then(({data}) => {
                console.log(data)
                if (data.code == 200) {
                    self.$Notice.success({
                        title: '保存成功！'
                    })
                    self.$set(this.userInfoObj, 'detail', true)
                } else if (data.code == 500) {
                    self.$Notice.warning({
                        title: '保存失败！',
                        desc: data.msg
                    })
                }
                self.isSubmit = true
            })
        },

    }
}
</script>

<style scoped>

.container {
    display: flex;
    align-items: flex-start;
    width: 1100px;
    margin: 20px auto;
}
.main {
    width: 800px;
    background: #fff;
    box-shadow: 0 0 1px #f1f3f4;
    padding: 20px 40px;
    box-sizing: border-box;
}
.userinfo {
    border-bottom: 1px solid #f2f3f3;
    padding-bottom: 20px;
}
.last-time {
    color: #aba3b0;
    font-size: 12px;
    margin-bottom: 20px;
}
.name {
    font-size: 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.detail {
    font-size: 14px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}
.detail__item {
    position: relative;
    margin-right: 20px;
}
.detail__item:not(:first-child) {
    padding-left: 20px;
}
.detail__item:not(:first-child)::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 1px;
    height: 15px;
    background: #ddd;
}   
.edit {
    display: flex;
    justify-content: flex-end;
    color: #478cc6;
    cursor: pointer;
    font-size: 14px;
}
.info {
    background: #f8f9fb;
    padding: 20px;

}
.info__title {
    margin-bottom: 20px;
    font-size: 12px;
}
.info__item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}
.info__name {
    font-size: 12px;
    margin-bottom: 5px;
}
.info__input {
    border: 1px solid #e3e7ed;
    padding: 5px 10px;
    width: 300px;
    height: 32px;
}
.info__select {
    width: 300px;
}
.btn-box {
    display: flex;
    justify-content: flex-end;
}
.cancel {
    margin-right: 10px;
    border: #ddd;
    padding: 5px 20px;
    border: 1px solid #ddd;
    cursor: pointer;
}
.confirm {
    color: #fff;
    background: #478cc6;
    padding: 5px 20px;
    cursor: pointer;
}

/* right */ 
.right {
    background: #fff;
    width: 280px;
    margin-left: 20px;
    box-shadow: 0 0 1px #f1f3f4;
    padding: 20px 40px;
    box-sizing: border-box;
}
.pdf__name {
    color: #478cc6;
    margin: 10px 0;
    display: block;
    cursor: pointer;
    width: 100%;
}
.pdf__name:hover {
    text-decoration: underline;
}
.pdf__reload {
    background: #478cc6;
    color: #fff;
    padding: 5px 20px;
    text-align: center;
    margin-top: 20px;
    cursor: pointer;
}
</style>
