import axios from "axios";

const axiosService = {
    get(url, params = {}, headers = {}) {
        return axios.get(url, {
            params,
            headers
        });
    },
    post(url, body = {}, config = {}) {
        return axios.post(url, body, config);
    },
    put(url, body = {}, config = {}) {
        return axios.put(url, body, config);
    },
    delete(url, config = {}) {
        return axios.delete(url, config);
    }
};

export default axiosService;
