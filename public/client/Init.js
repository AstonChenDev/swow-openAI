/**
 * 初始化类，websocket服务器类
 */

let Init = {
    ws: null,
    url: "",
    timer: 0,
    reback_times: 100,   //断线重连次数
    dubug: true,

    heartBeat: function () {
        this.heart_timer_id = setInterval(() => {
            if (this.ws.readyState === this.ws.OPEN) {
                let data = {};
                data['time'] = (new Date()).valueOf();
                this.send(data, SUB_CMD.SYS_HEART_ASK_REQ);
            } else {
                clearInterval(this.heart_timer_id);
            }
        }, 10000);
    },
    //启动websocket
    webSock: function (url) {
        this.url = url;
        let ws = new WebSocket(url);
        this.ws = ws;
        // ws.binaryType = "arraybuffer"; //设置为2进制类型  webSocket.binaryType = "blob" ;
        //连接回调
        ws.onopen = (evt) => {
            this.heartBeat();
            //获取用户状态
            this.log('系统提示: 连接服务器成功');
        };

        //消息回调
        ws.onmessage = (evt) => {
            if (!evt.data) return;
            this.recvCmd(JSON.parse(evt.data));
        };
        //关闭回调
        ws.onclose = (evt) => {
            //断线重新连接
            this.timer = setInterval(() => {
                if (this.reback_times === 0) {
                    clearInterval(this.timer);
                } else {
                    this.reback_times--;
                    this.webSock(this.url);
                }
            }, 1000);
            this.log('系统提示: 连接断开');
            // ws服务器重启后会主动断开客户端上次的长连接，为了避免客户端死循环重连，这里直接刷新页面，只保留一个可用的ws链接
            // window.location.reload();
        };
        //socket错误回调
        ws.onerror = (evt) => {
            this.log('系统提示: 服务器错误' + evt.type);
        };
        return this;
    },

    //处理消息回调命令字
    recvCmd: function (body) {
        let cmd = body['cmd'];
        let data = body['data'];
        this.log('websocket 收到服务端消息 <<<cmd="' + cmd + '"data="', body);
        console.log(JSON.stringify(body))
        //路由到处理地方
        let callback = ROUTE[cmd];

        if (body.code !== 200) {
            ROUTE[SUB_CMD.SYS_COMMON_RESP](body);
            return;
        }

        if (!callback) {
            this.log('func is valid');
            return;
        }
        callback(data)
    },

    //打印日志方法
    log: function (...msg) {
        if (this.dubug) {
            console.log(...msg);
        }
    },

    //发送数据
    send: function (data, cmd) {
        if (!data.hasOwnProperty('time')) {
            data.time = (new Date()).valueOf();
        }
        //this.ws.close();
        this.log("websocket 发送消息 >>>  cmd=" + cmd + "  data=", data);
        let pack_data = {
            data: data,
            cmd: cmd
        };
        //组装数据
        if (this.ws.readyState === this.ws.OPEN) {
            this.ws.send(JSON.stringify(pack_data));
        }
    }
}