<style lang="less">
    @import 'image-upload.less';
</style>

<template>
    <div>
        <div class="upload-list" v-for="(item, index) in uploadList" :key="index">
            <template v-if="item.status === 'finished'">
                <img :src="item.url + '?imageView2/1/w/200/h/200'">
                <div class="upload-list-cover">
                    <Icon v-if="big" type="ios-eye-outline" @click.native="showBig(item.url)"></Icon>
                    <Icon type="ios-trash-outline" @click.native="handleRemove(item)"></Icon>
                </div>
            </template>
            <template v-else>
                <Progress v-if="item.showProgress" :percent="item.percentage" hide-info></Progress>
            </template>
        </div>
        <Upload
            :ref="imageId"
            :show-upload-list="false"
            :default-file-list="defaultList"
            :on-success="handleSuccess"
            :format="['jpg','jpeg','png','gif']"
            :max-size="2048"
            :on-format-error="handleFormatError"
            :on-exceeded-size="handleMaxSize"
            :before-upload="handleBeforeUpload"
            multiple
            type="drag"
            action="/api/Front/uploadImage"
            style="display: inline-block;width:92px;">
            <div class="camera-box">
                <Icon type="ios-camera" size="20"></Icon>
            </div>
        </Upload>
        <Modal title="查看大图" v-model="imgPopup.visible" :style="{'z-index': 10000}" width="620">
            <img :src="imgPopup.url" v-if="imgPopup.visible" style="width: 100%">
        </Modal>
    </div>
</template>

<script>
export default {
  name: 'image-upload',
  props: {
    value: {
      type: Array,
      default () {
        return []
      }
    },
    maxNum: {
      type: Number,
      required: false,
      default: 1
    },
    big: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    // 计算属性的 getter
    defaultList: function () {
      var that = this
      var list = that.value
      var newlist = []
      for (var i in list) {
        newlist.push({
          name: 'img' + (i + 1),
          url: list[i]
        })
      }
      setTimeout(function () {
        that.uploadList = that.$refs[that.imageId].fileList
      }, 1)
      return newlist
    }
  },
  data () {
    return {
      imageId: 'vue-image-upload' + +new Date(),
      imgPopup: { // 点击大图
        url: '',
        visible: false
      },
      uploadList: []
    }
  },
  methods: {
    // 显示大图
    showBig (url) {
      this.imgPopup.url = url
      this.imgPopup.visible = true
    },
    // 上传文件之前的钩子,
    handleBeforeUpload () {
      const check = this.uploadList.length < this.maxNum
      if (!check) {
        this.$Notice.warning({
          title: '最多只能上传 ' + this.maxNum + ' 张图片。'
        })
      }
      return check
    },
    // 文件列表移除文件时的钩子
    handleRemove (file) {
      const fileList = this.$refs[this.imageId].fileList
      this.$refs[this.imageId].fileList.splice(fileList.indexOf(file), 1)
      this.$emit('input', this.filterArr())
    },
    // 文件上传成功时的钩子
    handleSuccess (res, file) {
      file.realurl = res.obj.filepath
      file.url = res.obj.realpath // 带域名
      file.name = res.obj.filename
      this.$emit('input', this.filterArr())
    },
    // 文件格式验证失败时的钩子
    handleFormatError (file) {
      this.$Notice.warning({
        title: '文件格式不正确',
        desc: '文件 ' + file.name + ' 格式不正确，请上传 jpg 或 png 格式的图片。'
      })
    },
    // 文件超出指定大小限制时的钩子
    handleMaxSize (file) {
      this.$Notice.warning({
        title: '超出文件大小限制',
        desc: '文件 ' + file.name + ' 太大，不能超过 2M。'
      })
    },
    filterArr () {
      var list = this.$refs[this.imageId].fileList
      var newlist = []
      list.forEach((item) => {
        newlist.push(item.url)
      })
      return newlist
    }
  },
  mounted () {
  }
}
</script>
