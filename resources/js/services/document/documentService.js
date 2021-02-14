import axiosService from "../axiosService";

export default {
    async classifyDocuments(payload) {
        const response = await axiosService.post('/documents/classify/tasks', payload);
        console.log(response);
        return response?.data;
    },
    async recognizeTask(taskId) {
        const response = await axiosService.post('/documents/recognize/task/' + taskId);
        console.log('recognize');
        console.log(response);
        return response?.data;
    },
    async saveIndividual(payload) {
        return await axiosService.post('/individuals/create', payload);
    },
    async replaceDocument(payload) {
        return await axiosService.post('/documents/replace', payload);
    },
    async saveDocumentForIndividual(payload) {
        return await axiosService.post('/documents/individuals/add', payload);
    }
}
