import axiosService from "../axiosService";

export default {
    async addManager(userData) {
        return await axiosService.post('/users/manager', userData);
    },
    async deleteUser(userId) {
        return await axiosService.delete('/users/' + userId);
    },
    async blockUser(userId) {
        return await axiosService.put('/users/block/' + userId);
    },
    async getUsers() {
        const response = await axiosService.get('/users/all');
        return response?.data;
    }
}
