
/**
 * 自定义数组的forEach方法
 * @param {一个数组} arr 
 * @param {一个方法} fn 
 */
export const forEach = (arr, fn) => {
    if (!arr.length || !fn) return;
    let i = -1;
    let len = arr.length;
    //循环数组
    while (++i < len) {
        let item = arr[i];
        fn(item, i, arr)
        // i++; 编程也是一种艺术...
    }
}

/**
 * 
 * @param {Array} targetarr 目标数组
 * @param {Array} arr  需要查询的数组
 */
export const hasOneOf = (targetarr, arr) => {
    return targetarr.some(_ => arr.indexOf(_) > -1);
}


/**
 * @param {String|Number} value 要验证的字符串或数值
 * @param {*} validList 用来验证的列表
 */
export function oneOf (value, validList) {
    for (let i = 0; i < validList.length; i++) {
      if (value === validList[i]) {
        return true
      }
    }
    return false
  }
  

export const getUnion = (arr1, arr2) => {
    return Array.from(new Set([...arr1, ...arr2]))
}


/**
 * @param {*} obj1 对象
 * @param {*} obj2 对象
 * @description 判断两个对象是否相等，这两个对象的值只能是数字或字符串
 */
export const objEqual = (obj1, obj2) => {
    const keysArr1 = Object.keys(obj1)
    const keysArr2 = Object.keys(obj2)
    if (keysArr1.length !== keysArr2.length) return false
    else if (keysArr1.length === 0 && keysArr2.length === 0) return true
    /* eslint-disable-next-line */
    else return !keysArr1.some(key => obj1[key] != obj2[key])
  }


  export const on = (() => {
    if (document.addEventListener) {
        return (element, event, handler) => {
            if (element && event && handler) {
                element.addEventListener(event, handler, false);
            }
        }
    } else {
        return (element, event, handler) => {
            if (element && event && handler) {
                element.attachEvent('on' + event, handler)
            }
        }
    }
})()

export const off = (() => {
    if (document.removeEventListener) {
        return (element, event, handler) => {
            if (element && event) {
                element.removeEventListener(event, handler, false);
            }
        }
    } else {
        return (element, event, handler) => {
            if (element && event) {
                element.detachEvent('on' + event, handler)
            }
        }
    }
})()