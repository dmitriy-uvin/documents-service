import axiosService from "../axiosService";

export default {
    async login(payload) {
        return axiosService.post('/login', payload);
    }
}
