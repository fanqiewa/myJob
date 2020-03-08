let util = {

};

util.toPriceInt = (value) => {
    return parseFloat(parseInt(value, 10) / 100)
};


// 时间戳转日期
util.formatTime = function(timestamp) {
    if (!timestamp) {
        return ''
    }
    var date = new Date(timestamp * 1000);
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    m = m < 10 ? ('0' + m) : m;
    var d = date.getDate();
    d = d < 10 ? ('0' + d) : d;
    var h = date.getHours();
    h = h < 10 ? ('0' + h) : h;
    var minute = date.getMinutes();
    var second = date.getSeconds();
    minute = minute < 10 ? ('0' + minute) : minute;
    second = second < 10 ? ('0' + second) : second;
    return y + '-' + m + '-' + d + ' ' + h + ':' + minute;
};


// 时间戳转日期
util.formatTimes = function(timestamp) {
    if (!timestamp) {
        return ''
    }
    var date = new Date(timestamp * 1000)
    var y = date.getFullYear()
    var m = date.getMonth() + 1
    m = m < 10 ? ('0' + m) : m
    var d = date.getDate()
    d = d < 10 ? ('0' + d) : d
    var h = date.getHours()
    h = h < 10 ? ('0' + h) : h
    var minute = date.getMinutes()
    var second = date.getSeconds()
    minute = minute < 10 ? ('0' + minute) : minute
    second = second < 10 ? ('0' + second) : second
    return y + '-' + m + '-' + d + ' ' + h + ':' + minute + ':' + second
}

util.toPrice = (value) => {
    return parseFloat(parseInt(value, 10) / 100).toFixed(2)
};

// 判断是否为空
util.isEmpty = function(value) {
    return value === '' || value === undefined || value === null || (Array.isArray(value) && value.length === 0) || (Object.prototype.isPrototypeOf(value) && Object.keys(value).length === 0)
};


//验证数字类型且不能为空
util.validateNumber = (rule, value, callback) => {
    if (!value) {
        return callback(new Error('不能为空'))
    }
    setTimeout(() => {
        if (!Number(value)) {
            callback(new Error('必须为数字类型'))
        } else {
            callback()
        }
    }, 1000)
}

//验证数字类型
util.validateNumbers = (rule, value, callback) => {
  if(value) {
    setTimeout(() => {
        if (!Number(value)) {
            callback(new Error('必须为数字类型'))
        } else {
            callback()
        }
    }, 1000)
  } else {
    callback()
  }
}

//验证日期范围类型
util.validateDates = (rule, value, callback) => {
  if(value) {
    setTimeout(() => {
        if (!value instanceof Array || !value[0] || !value[1]) {
            callback(new Error('日期不能为空'))
        } else {
            callback()
        }
    }, 1000)
  } else {
    callback()
  }
}

// 格式化日期
util.formatDate = function (date, type) {
    if (date instanceof Date == false) {
      return date
    }
    var Y = date.getFullYear()
    var M = add0((date.getMonth() + 1))
    var D = add0(date.getDate())
    var h = add0(date.getHours())
    var m = add0(date.getMinutes())
    var s = add0(date.getSeconds())
    if (type == 'hm') { // 时分
      return h + ':' + m
    } else if (type == 'hms') { // 时分秒
      return h + ':' + m + ':' + s
    } else if (type == 'ymd') { // 年月日
      return Y + '-' + M + '-' + D
    } else { // 年月日时分秒
      return Y + '-' + M + '-' + D + ' ' + h + ':' + m + ':' + s
    }
    function add0 (m) { // 保留年月日时分秒中的0
      return m < 10 ? '0' + m : m
    }
  }
// 日期转时间戳
util.dateTimestamp = function (date) {
    return (Date.parse(new Date(date))) / 1000
}


export default util;