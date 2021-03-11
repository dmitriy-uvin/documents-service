import documentTypes from "../constants/documentTypes";
import fieldTypes from "../constants/fieldTypes";

const nameKeys = [
    'name',
    'first_name',
    'name_rus',
    'name_eng',
    'name_ukr',
    'a_vehicle_owner_name',
    'a_veh_driver_name',
    'b_vehicle_owner_name',
    'b_veh_driver_name',
    'legal_name',
    'legal_name_rus',
];
const surnameKeys = [
    'surname',
    'surname_rus',
    'surname_eng',
    'surname_ukr',
    'a_vehicle_owner_surname',
    'a_veh_driver_surname',
    'b_vehicle_owner_surname',
    'b_veh_driver_surname',
];
const patronymicKeys = [
    'third_name',
    'patronymic',
    'patron',
    'other_name',
    'other_names',
    'patter',
    'a_vehicle_owner_patronym',
    'a_veh_driver_patronym',
    'b_vehicle_owner_patronym',
    'b_veh_driver_patronym',
    'third',
];
const fioKeys = [
    'fio',
    'cardholder_name',
    'born_full_name',
    // 'full_name'
];

const dateBirthKeys = [
    "date_of_birth",
    "a_date_of_birth",
    "b_date_of_birth",
];

export default {
    methods: {
        getFullName(individual) {
            let name = "";
            let surname = "";
            let patronymic = "";
            let fio = "";

            if (individual.documents.length) {
                const firstDocument = individual.documents[0];

                firstDocument.fields.map(field => {
                    if (nameKeys.includes(field.type)) name = field.value;
                    if (surnameKeys.includes(field.type)) surname = field.value;
                    if (patronymicKeys.includes(field.type)) patronymic = field.value;
                    if (fioKeys.includes(field.type)) fio = field.value;
                });
            }

            let fullName = "";
            if (!fio) {
                fullName = surname + ' ' + name + ' ' + patronymic;
            } else {
                fullName = fio;
            }

            return fullName ? fullName : 'Неизвестно';
        },
        getDateBirth(individual) {
            let dateBirth = "";
            if (individual.documents.length && !dateBirth) {
                individual.documents.map(document => {
                    document.fields.map(field => {
                        if (dateBirthKeys.includes(field.type)) {
                            if (field.value) {
                                dateBirth = field.value;
                            }
                        }
                    });
                });
            }
            return dateBirth;
        },
        getName(individual) {
            let name = "";
            if (individual.documents) {
                individual.documents.map(document => {
                    document.fields.map(field => {
                        if (nameKeys.includes(field.type)) name = field.value;
                    });
                });
            }

            return name;
        },
        getSurname(individual) {
            let surname = "";
            individual.documents.map(document => {
                document.fields.map(field => {
                    if (surnameKeys.includes(field.type)) surname = field.value;
                });
            });
            return surname;
        },
        getPatronymic(individual) {
            let patronymic = "";
            individual.documents.map(document => {
                document.fields.map(field => {
                    if (patronymicKeys.includes(field.type)) patronymic = field.value;
                });
            });
            return patronymic;
        },
        getDocumentNameByKey(key) {
            return documentTypes.all[key] ? documentTypes.all[key] : key;
        },
        getFieldNameByKey(key) {
            return fieldTypes.all[key] ? fieldTypes.all[key] : key;
        },
        cannotBeRecognized(key) {
            return Object.keys(documentTypes.notRecognizable).includes(key);
        },
        canBeRecognized(key) {
            return Object.keys(documentTypes.recognizable).includes(key);
        },
        canBeUpload(key) {
            return key !== 'not_document';
        },
        canBeDuplicated(key) {
            return Object.keys(documentTypes.canBeDuplicated).includes(key);
        }
    }
}
