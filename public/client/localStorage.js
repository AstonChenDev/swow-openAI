class localStorage {
    get(key) {
        return this.handleLocalStorage('get', key);
    }

    set(key, value) {
        return this.handleLocalStorage('set', key, value);
    }

    /**
     * 处理本地存储方法
     * @param method
     * @param key
     * @param value
     * @returns {string|boolean}
     */
    handleLocalStorage(method, key, value = '') {
        switch (method) {
            case 'get' : {
                let temp = window.localStorage.getItem(key);
                if (temp) {
                    return temp
                } else {
                    return false
                }
            }
            case 'set' : {
                window.localStorage.setItem(key, value);
                break
            }
            case 'remove': {
                window.localStorage.removeItem(key);
                break
            }
            default : {
                return false
            }
        }
    }
}