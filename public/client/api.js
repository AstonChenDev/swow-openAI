async function api_get(_url, _params, _title) {
    try {
        let response = await axios.get(_url, {params: _params});
        if (response.status !== 200) {
            this.error = true;
            this.message = "请求数据异常[" + response.status + "]";
            //console.log(this.message);
            console.log(_title, this.message, "error");
            return null;
        }

        let data = response.data;
        if (data && data.code === 200) {
            return data.data;
        } else {
            this.error = true;
            this.message = data.message;
            console.log(_title, this.message, "error");
        }
    } catch (exception) {
        this.error = true;
        this.message = "请求数据异常[" + exception + "]";
        console.log(_title, this.message, "error");
    }

}

async function api_post(_method, _url, _data, header = {}, origin = false) {
    try {
        let response = await axios({
            method: _method,
            url: _url,
            headers: header,
            data: _data,
        });
        if (response.status !== 200) {
            let message = "请求数据异常[" + response.status + "]";
            //console.log(message);
            console.log('error: ' + message);
            return false;
        }
        let data = response.data;

        if (origin) {
            return data;
        }
        //console.log(data)
        if (data && data.code === 200) {
            let res = data.data;
            if (Array.prototype.isPrototypeOf(res) && res.length === 0) {
                let message = '返回空数据';
                //console.log(message);
                console.log('error: ' + message);
                return false;
            }
            return res;
        } else {
            let message = data.message;
            console.log('error: ' + message);
            return false;
        }
    } catch (exception) {
        let message = "请求数据异常[" + exception + "]";
        console.log('error: ' + message);
        return false;
    }
}

