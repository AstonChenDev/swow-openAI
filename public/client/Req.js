/**发请求命令字处理类*/

let Req = {
    //发请求
    send: function (obj, data, scmd, cmd = MAIN_CMD.CMD_QUIZ_PHASE) {
        data.time = (new Date()).valueOf();
        obj.send(data, cmd, scmd);
    },
    query: async function (_url, _data = {}, header = {}, origin = false) {
        return await api_get(_url, _data, header, origin)
    },
    post: async function (_url, _data, header = {}, origin = false) {
        return await api_post('post', _url, _data, header, origin)
    }
}