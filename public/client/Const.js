/** 主命令字定义 **/
const MAIN_CMD = {
    /** 系统类（主命令字）- 客户端使用 **/
    CMD_SYS: 1,
    /** 游戏类（主命令字）- 客户端使用 **/
    CMD_QUIZ_PHASE: 201,
}


/** 子命令字定义 **/
const SUB_CMD = {
    /*请求指令*/
    SYS_HEART_ASK_REQ: 1, //心跳请求
    SYS_CHAT_REQ: 4, //聊天请求
    SYS_ENTER_CHAT_REQ: 6,//进入聊天

    ROOM_CHAT_REQ: 20, //聊天消息请求，客户端使用
    ROOM_CREATE_REQ: 21, //创建房间请求，客户端使用
    ROOM_JOIN_REQ: 22, //加入房间请请求，客户端使用
    ROOM_LEAVE_REQ: 23, //离开房间请求，客户端使用
    ROOM_MATCH_REQ: 24, //房间匹配请求，客户端使用
    CHANGE_PRIVATE_ROOM_CONF_REQ: 25, //请求改变私有房配置
    ROOM_KICK_USER_REQ: 27, //私有房踢人请求

    GAME_NEXT_QUESTION_REQ: 50,//请求下一题
    GAME_START_REQ: 51, //游戏开始请求，客户端使用
    GAME_RESTART_REQ: 52, //重开游戏请求，客户端使用
    GAME_RECONNECT_REQ: 53, //游戏重连请求，客户端使用
    GAME_ANSWER_REQ: 54, //回答问题请求，客户端使用
    GAME_GIVE_UP_REQ: 55, //放弃游戏请求，客户端使用
    GAME_USE_PROP_REQ: 56, //使用道具请求，客户端使用
    GAME_LIKE_REQ: 58, //点赞请求
    GAME_ASK_HOUR_GLASS_REQ: 61, //拒绝使用加时卡广播
    GAME_ASK_RESURRECT_REQ: 64, //拒绝使用复活卡请求

    /*响应指令*/
    SYS_HEART_ASK_RESP: 1, //心跳响应
    SYS_LOGIN_RESP: 2, //登录响应
    SYS_COMMON_RESP: 3, //通用响应
    SYS_CHAT_RESP: 4, //聊天响应

    ROOM_CHAT_MSG_RESP: 20, //聊天消息响应
    ROOM_CREATE_RESP: 21, //创建房间响应
    ROOM_JOIN_RESP: 22, //加入房间请响应
    ROOM_LEAVE_RESP: 23, //离开房间响应
    ROOM_MATCH_RESP: 24, //房间匹配响应
    CHANGE_PRIVATE_ROOM_CONF_RESP: 25, //改变私有房配置响应

    GAME_NEXT_QUESTION_RESP: 50,//响应下一题
    GAME_START_RESP: 51, //游戏开始响应
    GAME_RESTART_RESP: 52, //重开游戏响应
    GAME_RECONNECT_RESP: 53, //游戏重连响应
    GAME_ANSWER_RESP: 54, //回答问题响应
    GAME_ALL_ANSWERED_RESP: 55, //所有玩家都回答完了响应
    GAME_USE_PROP_RESP: 56, //使用道具响应
    GAME_OVER_RESP: 57,//游戏结束响应
    GAME_LIKE_RESP: 58, //点赞响应
    GAME_SPIN_TOPIC_RESP: 59, //大转盘题目主题广播
    GAME_ROUND_SETTLE_RESP: 60, //每轮结算广播
    GAME_ASK_HOUR_GLASS_RESP: 61, //询问是否使用加时卡广播
    GAME_ASK_REJOIN_PRIVATE_ROOM_RESP: 62, //询问是否重新加入私有房提示
    GAME_BEFORE_GAME_START_COUNT_DOWN_RESP: 63, //游戏开始之前倒计时
    GAME_ASK_RESURRECT_RESP: 64, //询问是否使用复活卡广播
    GAME_DISPLAY_SURVIVALS_RESP: 65, //展示生存者信息
    GAME_DISPLAY_PLAYER_ANSWERED_RESP: 66, //展示所有玩家答题情况响应
    GAME_TOPIC_RESP: 67, //游戏开始前 题目主题广播
    GAME_DESTROY_RESP: 68, //游戏销毁广播

    USER_PUSH_MESSAGE: 121 //服务端消息推送
}

//道具
const POWER_UPS = {
    PROP_HOUR_GLASS: 1000003,
    PROP_COIN: 1000004,
    PROP_DIAMOND: 1000005,
    PROP_TL: 1000006,
    PROP_RESURRECT: 1000008
};

const TYPE_TIP = 1;
const TYPE_BROADCAST = 2;
/**
 * 路由规则，key主要命令字=》array(子命令字对应策略类名)
 * 每条客户端对应的请求，路由到对应的逻辑处理类上处理
 *
 */
const ROUTE = {
    [MAIN_CMD.CMD_SYS]: {
        [SUB_CMD.SYS_HEART_ASK_RESP]: {
            [TYPE_TIP]: app.heartAsk
        },
        [SUB_CMD.SYS_CHAT_RESP]: {
            [TYPE_BROADCAST]: app.chatResp
        },
        [SUB_CMD.SYS_COMMON_RESP]: {
            [TYPE_TIP]: app.commonResp
        },
        [SUB_CMD.SYS_LOGIN_RESP]: {
            [TYPE_TIP]: app.longinResp
        },
        [SUB_CMD.USER_PUSH_MESSAGE]: {
            [TYPE_TIP]: app.receiveMsg
        }
    },
    [MAIN_CMD.CMD_QUIZ_PHASE]: {
        [SUB_CMD.ROOM_CHAT_MSG_RESP]: {
            [TYPE_BROADCAST]: app.chatMsgResp
        },
        [SUB_CMD.GAME_START_RESP]: {
            [TYPE_BROADCAST]: app.startResp,
            [TYPE_TIP]: app.startResp,
        },
        [SUB_CMD.GAME_NEXT_QUESTION_RESP]: {
            [TYPE_BROADCAST]: app.nextQuestionResp
        },
        [SUB_CMD.GAME_ANSWER_RESP]: {
            [TYPE_TIP]: app.answerResp,
            [TYPE_BROADCAST]: app.othersAnswerResp,
        },
        [SUB_CMD.GAME_USE_PROP_RESP]: {
            [TYPE_TIP]: app.usePropResp
        },
        [SUB_CMD.GAME_OVER_RESP]: {
            [TYPE_BROADCAST]: app.gameOver
        },
        [SUB_CMD.ROOM_CREATE_RESP]: {
            [TYPE_BROADCAST]: app.roomCreateResp
        },
        [SUB_CMD.ROOM_JOIN_RESP]: {
            [TYPE_BROADCAST]: app.roomJoinResp
        },
        [SUB_CMD.CHANGE_PRIVATE_ROOM_CONF_RESP]: {
            [TYPE_BROADCAST]: app.roomChangeConfigResp
        },
        [SUB_CMD.GAME_LIKE_RESP]: {
            [TYPE_TIP]: app.likeResp
        },
        [SUB_CMD.GAME_SPIN_TOPIC_RESP]: {
            [TYPE_BROADCAST]: app.showSpin
        },
        [SUB_CMD.GAME_ROUND_SETTLE_RESP]: {
            [TYPE_BROADCAST]: app.roundSettleResp
        },
        [SUB_CMD.ROOM_LEAVE_RESP]: {
            [TYPE_TIP]: app.roomLeaveTip,
            [TYPE_BROADCAST]: app.roomLeaveResp,
        },
        [SUB_CMD.GAME_ASK_HOUR_GLASS_RESP]: {
            [TYPE_BROADCAST]: app.askHourGlassResp
        },
        [SUB_CMD.GAME_ASK_RESURRECT_RESP]: {
            [TYPE_BROADCAST]: app.askResurrectResp
        },
        [SUB_CMD.ROOM_MATCH_RESP]: {
            [TYPE_BROADCAST]: app.roomMatchResp
        },
        [SUB_CMD.GAME_BEFORE_GAME_START_COUNT_DOWN_RESP]: {
            [TYPE_BROADCAST]: app.beforeGameStartCountDown
        },
        [SUB_CMD.GAME_DISPLAY_SURVIVALS_RESP]: {
            [TYPE_BROADCAST]: app.displaySurvivals
        },
        [SUB_CMD.GAME_DISPLAY_PLAYER_ANSWERED_RESP]: {
            [TYPE_BROADCAST]: app.displayUserOptions
        },
        [SUB_CMD.GAME_ASK_REJOIN_PRIVATE_ROOM_RESP]: {
            [TYPE_TIP]: app.roomAskRejoinResp
        },
        [SUB_CMD.GAME_ALL_ANSWERED_RESP]: {
            [TYPE_BROADCAST]: app.gameAllAnsweredResp
        },
        [SUB_CMD.GAME_TOPIC_RESP]: {
            [TYPE_BROADCAST]: app.gameTopicResp
        },
        [SUB_CMD.GAME_DESTROY_RESP]: {
            [TYPE_BROADCAST]: app.gameDestroyResp
        }
    },
}