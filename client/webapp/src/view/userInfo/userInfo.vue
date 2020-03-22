<template>
    <div>
        <div class="container">
            <div class="main">
                <div class="userinfo">
                    <div class="last-time">最后更新时间{{formValidate.updated_at}}</div>
                    <template v-if="userInfoObj.detail">
                        <div class="name">
                            <span>{{formValidate.name}}</span>
                            <div class="edit" @click="toggleEdit('detail')">
                                <Icon type="ios-create-outline" size="22" />
                                <span>编辑</span>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail__item">
                                <Icon type="ios-bus-outline" size="22"/>
                                <span>{{formValidate.job_education}}</span>
                            </div>
                            <div class="detail__item">
                                <Icon type="ios-book-outline" size="22"/>
                                <span>{{formValidate.job_experience}}</span>
                            </div>
                            <div class="detail__item">
                                <Icon type="ios-ribbon-outline" size="22"/>
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
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">学历</div>
                                    <input type="text" class="info__input" v-model="formValidate.job_education">
                                </div>
                                <div class="item-left">
                                    <div class="info__name">工作经验</div>
                                    <input type="text" class="info__input" v-model="formValidate.job_experience">
                                </div>
                            </div>
                            <div class="btn-box">
                                <div class="cancel" @click="toggleEdit('detail')">取消</div>
                                <div class="confirm" @click="saveUserInfo">完成</div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="userinfo">
                    <div class="userinfo__title">个人优势</div>
                    <template v-if="userInfoObj.advantage">
                            <div class="edit-box">
                                <div class="edit" @click="toggleEdit('advantage')">
                                    <Icon type="ios-create-outline" size="22" />
                                    <span>编辑</span>
                                </div>
                            </div>
                            <div class="advantage">{{formValidate.advantage}}</div>
                    </template>
                    <template v-else>
                        <div class="info">
                            <Input class="advantage-input" v-model="formValidate.advantage" type="textarea" :rows="4" placeholder="请输入个人优势" />
                            <div class="btn-box">
                                <div class="cancel" @click="toggleEdit('advantage')">取消</div>
                                <div class="confirm" @click="saveAdvantage">完成</div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="userinfo">
                    <div class="userinfo__title">期望职位</div>
                    <template v-if="userInfoObj.position">
                            <div class="edit-box">
                                <div class="edit" @click="toggleEdit('position')">
                                    <Icon type="ios-create-outline" size="22" />
                                    <span>添加</span>
                                </div>
                            </div>
                            <div class="expect" v-for="(item, index) of formValidate.position" :key="item.id" @mouseenter="showEditDelete(index, 'position')" @mouseleave="hideEditDelete()">
                                <div class="expect__left">
                                    <div class="expect__item"><Icon type="md-navigate" />{{getSalaryText(item.salary_id)}}</div>
                                    <div class="expect__item"><Icon type="logo-usd" />{{getPositionText(item.position_id[2])}}</div>
                                    <div class="expect__item"><Icon type="ios-paw-outline" />{{getCityText(item.city_id[1])}}</div>
                                </div>
                                <div class="work__right" v-if="selectPositionIndex == index">
                                    <div class="work__edit" @click="editPosition(index)">编辑</div>
                                    <div class="work__delete" @click="delPosition(index)">删除</div>
                                </div>
                            </div>
                    </template>
                    <template v-else>
                        <div class="info">
                            <div class="edit-position">编辑期望职位</div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">期望职位</div>
                                    <Cascader v-model="position.positionValue" :data="positionList" trigger="hover" style="width: 300px"></Cascader>
                                </div>
                                <div class="item-left">
                                    <div class="info__name">城市</div>
                                    <Cascader v-model="position.areaValue" :data="areaList" trigger="hover" style="width: 300px"></Cascader>
                                </div>
                            </div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">期望薪资</div>
                                    <Select v-model="position.salaryValue" style="width:300px" label-in-value>
                                        <Option v-for="item in salaryList" :value="item.id" :key="item.id">{{ item.label }}</Option>
                                    </Select>
                                </div>
                            </div>

                            <div class="btn-box">
                                <div class="cancel" @click="toggleEdit('position')">取消</div>
                                <div class="confirm" @click="savePosition">完成</div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="userinfo">
                    <div class="userinfo__title">工作经历</div>
                    <template v-if="userInfoObj.work">
                            <div class="edit-box">
                                <div class="edit" @click="toggleEdit('work')">
                                    <Icon type="ios-create-outline" size="22" />
                                    <span>添加</span>
                                </div>
                            </div>
                            <div class="work">
                                <div class="work__item" v-for="(item, index) in formValidate.experience" :key="item.id"  @mouseenter="showEditDelete(index, 'work')" @mouseleave="hideEditDelete()">
                                    <div class="work__left">
                                        <div>公司名称：{{item.company}}</div>
                                        <div>所属部门：{{item.department}}</div>
                                        <div>在职时间：{{getTime(item.time_start)}} - {{getTime(item.time_end)}}</div>
                                        <div>工作内容：{{item.work}}</div>
                                    </div>
                                    <div class="work__right" v-if="selectWorkIndex == index">
                                        <div class="work__edit" @click="editExperience(index)">编辑</div>
                                        <div class="work__delete" @click="delExperience(index)">删除</div>
                                    </div>
                                </div>
                            </div>
                    </template>
                    <template v-else>
                        <div class="info">
                            <div class="edit-position">编辑工作经历</div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">公司名称</div>
                                    <input type="text" class="info__input" v-model="work.company">
                                </div>
                                <div class="item-left">
                                    <div class="info__name">所属部门</div>
                                    <input type="text" class="info__input" v-model="work.department">
                                </div>
                            </div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">在职时间</div>
                                    <DatePicker type="datetimerange" placeholder="请选择在职时间" style="width: 400px" v-model='work.time'></DatePicker>
                                </div>
                            </div>
                            <div class="info__item">
                                <div class="item-left">
                                    <div class="info__name">工作内容</div>
                                    <Input class="work-input" v-model="work.work" type="textarea" :rows="4" placeholder="请输入工作内容" />
                                </div>
                            </div>

                            <div class="btn-box">
                                <div class="cancel" @click="toggleEdit('work')">取消</div>
                                <div class="confirm" @click="saveWork">完成</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 右边 -->
            <div class="right">
                <div class="right__title">附件管理</div>
                <Dropdown placement="left-start" :transfer="true"> 
                    <div class="pdf__name">{{formValidate.pdf_name}}</div>
                    <DropdownMenu slot="list">
                        <DropdownItem>
                            <div @click="deletePdf">删除</div>
                        </DropdownItem>
                        <DropdownItem>
                            <div @click="downPdf">下载简历</div>
                        </DropdownItem>
                    </DropdownMenu>
                </Dropdown>
                <div class="pdf__time" v-if="formValidate.pdf_update">更新时间：{{formValidate.pdf_update}}</div>
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
            selectPositionIndex: null,
            selectWorkIndex: null,
            isSubmit: true,
            userInfoObj: {
                detail: true,
                advantage: true,
                position: true,
                work: true,
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
                updated_at: '',
                name: '',
                job_status: 1,
                sex: 'male',
                birthday: '',
                wx_code: '',
                phone: '',
                email: '',
                pdf_name: '',
                pdf_update: '',
                advantage: '',
                experience: [],
                job_experience: '',
                job_education: '',
            },
            salaryList: [], // 薪资列表
            areaList: [], // 区域列表
            positionList: [], // 职位列表
            position: {
                salaryValue: '',
                areaValue: [],
                positionValue: [],
            },
            positionAllList: [],
            cityAllList: [],
            work: {
                company: '',
                department: '',
                work: '',
                time: []
            }
        }
    },
    computed: {
    },
    created() {
        this.getSalaryList()
        this.getAreaList()
        this.getPositionList()
        this.getJobHunterInfo()
        this.getAllPositionList()
    },
    methods: {

        /** -------------------------------工具函数------------------------------------- */
        
        /**
         * 格式化时间
         */
        getTime(time) {
            return util.formatTime(time)
        },

        /** -------------------------------获取数据------------------------------------- */
        
        /**
         * 获取求职值信息
         */
        getJobHunterInfo() {
            const self = this
            const account = Cookies.get('account')
            self.$axios.post(prefixApi + '/getJobHunterInfo', {account: account}).then(({data}) => {
                console.log(data)
                if (data.code == 200) {
                    let result = data.obj.result
                    data.obj.result.birthday = new Date(result.birthday)
                    data.obj.result.pdf_update = util.formatTimes(result.pdf_update)
                    data.obj.result.updated_at = util.formatTimes(result.updated_at)
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
         * 获取薪资列表
         */
        getSalaryList() {
            const self = this
            self.$axios.post(prefixApi + '/getSalaryList').then(({data}) => {
                if (data.code == 200) {
                    let list = data.obj.list
                    list.forEach(item => {
                        item.label = (item.price / 100000) + 'K'
                    })
                    self.salaryList = list
                } 
            })
        },

        /**
         * 获取区域列表
         */
        getAreaList() {
            const self = this
            self.$axios.post(prefixApi + '/getAreaList').then(({data}) => {
                if (data.code == 200) {
                    self.areaList = data.obj.list
                } 
            })
        },
        
        
        /**
         * 获取区域列表
         */
        getAllAreaList() {
            const self = this
            self.$axios.post(prefixApi + '/getAllAreaList').then(({data}) => {
                if (data.code == 200) {
                    self.cityAllList = data.obj.list
                } 
            })
        },

        /**
         * 获取职位列表
         */
        getPositionList() {
            const self = this
            self.$axios.post(prefixApi + '/getPositionList').then(({data}) => {
                if (data.code == 200) {
                    self.positionList = data.obj.list
                } 
            })
        },

        /**
         * 获取职位列表
         */
        getAllPositionList() {
            const self = this
            self.$axios.post(prefixApi + '/getAllPositionList').then(({data}) => {
                if (data.code == 200) {
                    self.positionAllList = data.obj.list
                } 
            }).then(() => {
                self.getAllAreaList()
            })
        },
        
        /** --------------------------------事件处理------------------------------------ */

        showEditDelete(index, type) {
            if (type == 'position') {
                this.selectPositionIndex = index
                return
            }
            if (type == 'work') {
                this.selectWorkIndex = index
                return
            }
        },
        hideEditDelete() {
            this.selectPositionIndex = null
            this.selectWorkIndex = null
        },
        /**
         * 获取城市名称
         */
        getCityText(id) {
            const cityList = this.cityAllList
            for (let item of cityList) {
                if (item.id == id) {
                    return item.name
                }
            }
        },  

        /**
         * 获取职位名称
         */
        getPositionText(id) {
            const positionList = this.positionAllList
            for (let item of positionList) {
                if (item.id == id) {
                    return item.name
                }
            }
        },  

        /**
         * 获取职位名称
         */
        getSalaryText(id) {
            const salaryList = this.salaryList
            for (let item of salaryList) {
                if (item.id == id) {
                    return item.price / 100000 + 'K'
                }
            }
        },  

        /**
         * 删除pdf
         */
        deletePdf() {
            let self = this
            self.$Modal.confirm({
                title: '请确认',
                content: `确认删除简历？`,
                onOk: () => {   
                    const filepath = self.formValidate.pdf
                    let postData = {
                        account: Cookies.get('account'),
                        filepath
                    }
                    if (!self.isSubmit) {
                        return
                    }
                    self.isSubmit = false
                    self.$axios.post(prefixApi + "/delete", postData).then(({ data }) => {
                        console.log(data)
                        if (data.code == 200) {
                            self.$Notice.success({
                                title: '成功提示',
                                desc: `删除成功！`
                            })
                            self.$set(self.formValidate, 'pdf_name', '')
                        } else if (data.code == 500) {
                            self.$Notice.warning({
                                title: '失败提示',
                                desc: data.msg
                            })
                        }
                        self.isSubmit = true
                    })
                }
            })
        },

        /**
         * 选择pdf
         */
        choicePdf(){
            this.$refs.filElem.dispatchEvent(new MouseEvent('click')) 
        },
        
        /**
         * 获取pdf
         */
        getFile(){
            const self = this
            const inputFile = this.$refs.filElem.files[0]
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
                self.$axios.post(prefixApi + '/uploadFile', forms, configs).then(({data}) => {
                    if (data.code == 200) {
                        self.$Notice.success({
                            title: '上传成功！'
                        })
                        self.formValidate.pdf = data.obj.filepath
                    }
                    
                })
            } else {
                return
            }
        },

        downPdf() {
            const url = this.formValidate.pdf
            const filename = this.formValidate.pdf_name
            window.location.href = prefixApi + `/down?filepath=${url}&filename=${filename}`
        },

        /**
         * 改变编辑
         */
        toggleEdit(type) {
            this.$set(this.userInfoObj, type, !this.userInfoObj[type])
            if (type == 'work') {
                this.work.id = ''
            }
            if (type == 'position') {
                this.position.id = ''
            }
        },

        /**
         * 编辑工作经历
         */
        editExperience(index) {
            const experience = this.formValidate.experience
            this.work.id = experience[index].id
            this.work.company = experience[index].company
            this.work.department = experience[index].department
            this.work.work = experience[index].work
            this.work.time[0] = new Date(experience[index].time_start)
            this.work.time[1] = new Date(experience[index].time_end)
            this.$set(this.userInfoObj, 'work', false)
        },

        /**
         * 编辑工作经历
         */
        editPosition(index) {
            const position = this.formValidate.position
            this.position.id = position[index].id
            this.position.positionValue = position[index].position_id
            this.position.areaValue = position[index].city_id
            this.position.salaryValue = position[index].salary_id
            this.$set(this.userInfoObj, 'position', false)
        },

        /**
         * 删除工作经历
         */
        delExperience(index) {
            let self = this
            const experience = this.formValidate.experience
            const experience_id = experience[index].id
            
            self.$Modal.confirm({
                title: "是否确认删除行？",
                onOk: () => {
                    if (!self.isSubmit) {
                        return
                    }
                    self.isSubmit = false
                    self.$axios.post(prefixApi + "/deleteWork", {"experience_id": experience_id}).then(({data}) => {
                        if (data.code == 200) {
                            self.$Notice.success({
                                title: "成功提示",
                                desc: "删除成功！"
                            })
                            self.formValidate.experience.splice(index, 1)
                        } else {
                            self.$Notice.warning({
                                title: "失败提示",
                                desc: data.msg
                            });
                        }
                        self.isSubmit = true
                    })
                },
                onCancel: () => {
                    this.$Message.info('取消删除');
                }
            })
        },

        /**
         * 删除工作经历
         */
        delPosition(index) {
            let self = this
            const position = this.formValidate.position
            const jobhunter_position_id = position[index].id
            
            self.$Modal.confirm({
                title: "是否确认删除行？",
                onOk: () => {
                    if (!self.isSubmit) {
                        return
                    }
                    self.isSubmit = false
                    self.$axios.post(prefixApi + "/deletePosition", {"jobhunter_position_id": jobhunter_position_id}).then(({data}) => {
                        if (data.code == 200) {
                            self.$Notice.success({
                                title: "成功提示",
                                desc: "删除成功！"
                            })
                            self.formValidate.position.splice(index, 1)
                        } else {
                            self.$Notice.warning({
                                title: "失败提示",
                                desc: data.msg
                            });
                        }
                        self.isSubmit = true
                    })
                },
                onCancel: () => {
                    this.$Message.info('取消删除');
                }
            })
        },

        /**
         * 保存
         */
        saveUserInfo() {
            
            const self = this
            const { name, job_status, sex, birthday, wx_code, phone, email, job_education, job_experience } = self.formValidate
            const postData = {
                user_json: {
                    name,
                    job_status,
                    sex: sex == 'male' ? 1 : 2,
                    birthday: Date.parse(birthday) / 1000,
                    wx_code,
                    phone,
                    email,
                    job_education,
                    job_experience
                },
                account: Cookies.get('account')
            }

            if (!self.isSubmit) {
                return
            }
            self.isSubmit = false
            self.$axios.post(prefixApi + '/updateJobHunterInfo', postData).then(({data}) => {
                console.log(data)
                if (data.code == 200) {
                    self.$Notice.success({
                        title: '保存成功！'
                    })
                    self.$set(this.userInfoObj, 'detail', true)
                    self.formValidate.updated_at = util.formatTimes(Date.parse(new Date()) / 1000)
                } else if (data.code == 500) {
                    self.$Notice.warning({
                        title: '保存失败！',
                        desc: data.msg
                    })
                }
                self.isSubmit = true
            })
        },

        /**
         * 保存
         */
        saveAdvantage() {
            
            const self = this
            const { advantage } = self.formValidate
            const postData = {
                user_json: {
                    advantage
                },
                account: Cookies.get('account')
            }

            if (!self.isSubmit) {
                return
            }
            self.isSubmit = false
            self.$axios.post(prefixApi + '/updateJobHunterInfo', postData).then(({data}) => {
                console.log(data)
                if (data.code == 200) {
                    self.$Notice.success({
                        title: '保存成功！'
                    })
                    self.$set(this.userInfoObj, 'advantage', true)
                    self.formValidate.updated_at = util.formatTimes(Date.parse(new Date()) / 1000)
                } else if (data.code == 500) {
                    self.$Notice.warning({
                        title: '保存失败！',
                        desc: data.msg
                    })
                }
                self.isSubmit = true
            })
        },

        /**
         * 保存工作经历
         */
        saveWork() {
            const self = this
            const postData = {
                experience_json: {
                    company: self.work.company,
                    department: self.work.department,
                    work: self.work.work,
                    time_start: Date.parse(self.work.time[0]) / 1000,
                    time_end: Date.parse(self.work.time[1]) / 1000,
                },
                experience_id: self.work.id,
                jobhunter_id: self.formValidate.id
            }

            if (!self.isSubmit) {
                return
            }
            self.isSubmit = false
            self.$axios.post(prefixApi + '/updateWork', postData).then(({data}) => {
                console.log(data)
                if (data.code == 200) {
                    self.$Notice.success({
                        title: '保存成功！'
                    })
                    self.$set(this.userInfoObj, 'work', true)
                    self.formValidate.updated_at = util.formatTimes(Date.parse(new Date()) / 1000)
                } else if (data.code == 500) {
                    self.$Notice.warning({
                        title: '保存失败！',
                        desc: data.msg
                    })
                }
                self.isSubmit = true
            })
        },

        /**
         * 保存期望职位
         */
        savePosition() {
            const self = this
            const postData = {
                position_json: {
                    position_id: self.position.positionValue,
                    city_id: self.position.areaValue,
                    salary_id: self.position.salaryValue,
                },
                jobhunter_position_id: self.position.id,
                jobhunter_id: self.formValidate.id
            }

            if (!self.isSubmit) {
                return
            }
            self.isSubmit = false
            self.$axios.post(prefixApi + '/updatePosition', postData).then(({data}) => {
                console.log(data)
                if (data.code == 200) {
                    self.$Notice.success({
                        title: '保存成功！'
                    })
                    self.$set(this.userInfoObj, 'position', true)
                    self.formValidate.updated_at = util.formatTimes(Date.parse(new Date()) / 1000)
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
    width: 1200px;
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
    margin-bottom: 20px;
}
.last-time {
    color: #aba3b0;
    font-size: 12px;
    margin-bottom: 20px;
}
.name {
    font-size: 24px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.edit-box {
    font-size: 24px;
    display: flex;
    justify-content: flex-end;
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
.userinfo__title {
    position: relative;
    cursor: pointer;
    font-size:18px;
    padding-left: 10px;
}
.userinfo__title::after {
    position: absolute;
    content: "";
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    width: 2px;
    height: 20px;
    background: #478cc6;
}
.advantage-input {
    margin: 20px 0;
}
.advantage {
    padding: 10px;
    border-radius: 10px;
}
.advantage:hover {
    background: #f8f9fb;
}
.work__item {
    padding: 10px;
    border-radius: 10px;
    margin: 10px 0;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.work__item:hover {
    background: #f8f9fb;
}
.work__right {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    min-width: 200px;
}
.work__edit {
    color: #478cc6;
    cursor: pointer;
}
.work__delete {
    color: #478cc6;
    cursor: pointer;
    margin-left: 10px;
}
.work__edit:hover {
    opacity: .8;
    text-decoration: underline;
}
.work__delete:hover {
    opacity: .8;
    text-decoration: underline;
}
.edit-position {
    margin: 10px 0;
}
.expect {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-radius: 10px;
}
.expect:hover {
    background: #f8f9fb;
}
.expect__left, .expect__right {
    display: flex;
}
.expect__item {
    display: flex;
    align-items: center;
    margin-left: 20px;
}
.item-left {
    width: 100%;
}
.work-input {
    width: 100%;
}
</style>
