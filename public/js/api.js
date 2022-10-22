class Api {
    /**
     * @param {String} path 
     * @param {String} method 
     * @param {Object} query 
     * @param {Object} data 
     * @returns {Promise}
     */
    static async request(path, method, query, data) {
        const url = new URL(`${API_PATH}${path.startsWith('/') ? path : `/${path}`}`, API_BASE_URL);
        url.search = new URLSearchParams(query);
        
        /**
         * @type {RequestInit}
         */
        const options = {
            method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }

        if (typeof data !== 'undefined') {
            const dataFormat = new URLSearchParams();
            for (const name in data) dataFormat.append(name, data[name]);
            options.body = dataFormat;
        }
        
        const response = await fetch(url, options);

        let result;
        if (response.headers.get('Content-Type').startsWith('application/json')) {
            result = await response.json();
        } else result = await response.text();
        if (response.ok) return result;
        throw new Error(result.length > 0 ? result : response.statusText);
    }
    
    /**
     * @param {String} path 
     * @param {Object} query 
     * @returns {Promise}
     */
    static async get(path, query = {}) {
        return this.request(path, 'GET', query);
    }

    /**
     * @param {String} path 
     * @param {Object} data 
     * @param {Object} query 
     * @returns {Promise}
     */
    static async post(path, data, query = {}) {
        return this.request(path, 'POST', query, data);
    }

    /**
     * @param {String} path 
     * @param {Object} data 
     * @param {Object} query 
     * @returns {Promise}
     */
    static async put(path, data, query = {}) {
        return this.request(path, 'PUT', query, data);
    }

    /**
     * @param {String} path 
     * @param {Object} query 
     * @returns {Promise}
     */
    static async delete(path, query = {}) {
        return this.request(path, 'DELETE', query);
    }
}

export default Api;