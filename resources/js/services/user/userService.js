import axiosService from "../axiosService";

export default {
    async addManager(userData) {
        return await axiosService.post('/users/manager', userData);
    }
}
