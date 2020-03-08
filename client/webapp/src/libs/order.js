/**
 * 把数组中的数据转换一下 ---------------  【处理数据函数】
 */
export const transData = (arr, paramsStrArray, fn) => {  //arguments [{},{}]  ["str1", "str2"]  function
    if (!(typeof arr == "object") && arr !== null) return
    if (typeof paramsStrArray == "string") {
        paramsStrArray = [paramsStrArray]
    } 
    if (!Array.isArray(arr)) {
        arr = [arr]
    }
    arr.forEach(item => {
        paramsStrArray.forEach(ele => {
            item[ele] = fn(item[ele])
        })
    })
}