import axiosService from "../axiosService";

export default {
    async uploadDocuments(payload) {
        return await axiosService.post('/documents/upload', payload);
    }
}
