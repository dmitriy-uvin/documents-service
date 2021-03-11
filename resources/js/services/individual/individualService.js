import axiosService from "../axiosService";

export default {
    async getIndividualUsers(params) {
        const response = await axiosService.get('/individuals/all', params);
        return response?.data;
    },
    async getIndividualUserById(id) {
        const response = await axiosService.get('/individuals/get/' + id);
        return response?.data;
    },
    async search(payload) {
        const response = await axiosService.post('/individuals/search', payload);
        return response?.data;
    }
}
