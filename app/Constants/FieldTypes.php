<?php

namespace App\Constants;

class FieldTypes
{
    public static function fieldTypes(): array
    {
        return [
            "number" => "Номер",
            "month" => "Expiration Date: month",
            "year" => "Год изготовления ТС",
            "cardholder_name" => "Cardholder name",
            "born_full_name" => "ФИО родившегося",
            "date_of_birth" => "Дата рождения",
            "father_full_name" => "ФИО (отец)",
            "mother_full_name" => "ФИО (мать)",
            "place_of_birth" => "Место рождения",
            "record_number" => "Record No.",
            "issuer" => "Орган выдачи, номер",
            "doc_series" => "Серия ПТС",
            "doc_number" => "Номер ПТС",
            "full_name" => "Полное наименование юрлица",
            "date_of_death" => "Дата смерти",
            "place_of_death" => "Место смерти",
            "spouse_1_full_name" => "ФИО супруга",
            "spouse_1_date_of_birth" => "Дата рождения супруга",
            "spouse_2_full_name" => "ФИО супруги",
            "spouse_2_date_of_birth" => "Дата рождения супруги",
            "date_of_divorce" => "Дата расторжения брака",
            "category_a" => "Категория A",
            "category_b" => "Категория B",
            "category_c" => "Категория C",
            "category_d" => "Категория D",
            "category_e" => "Категория E",
            "special_marks" => "Особые отметки",
            "series_top" => "Серия СТС (верхняя часть)",
            "number_top" => "Номер СТС (верхняя часть)",
            "series_bottom" => "Серия СТС (нижняя часть)",
            "number_bottom" => "Номер СТС (нижняя часть)",
            "surname" => "Собственник: фамилия (на английском)",
            "name" => "Собственник: имя (на английском)",
            "third_name" => "Отчество",
            "date_of_issue" => "Дата выдачи",
            "valid_before" => "Действителен до",
            "category_a_begin" => "Категория A: начало действия",
            "category_a_end" => "Категория A: конец действия",
            "category_b_begin" => "Категория B: начало действия",
            "category_c_begin" => "Категория C: начало действия",
            "category_b_end" => "Категория B: конец действия",
            "category_c_end" => "Категория C: конец действия",
            "category_d_begin" => "Категория D: начало действия",
            "category_d_end" => "Категория D: конец действия",
            "category_be_begin" => "Категория Be: начало действия",
            "category_be_end" => "Категория Be: конец действия",
            "category_ce_begin" => "Категория Ce: начало действия",
            "category_ce_end" => "Категория Ce: конец действия",
            "category_de_begin" => "Категория De: начало действия",
            "category_de_end" => "Категория De: конец действия",
            "category_tm_begin" => "Категория Tm: начало действия",
            "category_tm_end" => "Категория Tm: конец действия",
            "category_tb_begin" => "Категория Tb: начало действия",
            "category_tb_end" => "Категория Tb: конец действия",
            "series_number" => "Серия и номер документа",
            "patronymic" => "Patronymic",
            "date_from" => "Дата выдачи",
            "date_end" => "Действительно до",
            "place_of_issue" => "Место выдачи",
            "category" => "Категория ТС",
            "category_a1_begin" => "Категория A1: начало действия",
            "category_a1_end" => "Категория A1: конец действия",
            "category_b1_begin" => "Категория B1: начало действия",
            "category_b1_end" => "Категория B1: конец действия",
            "category_c1_begin" => "Категория C1: начало действия",
            "category_c1_end" => "Категория C1: конец действия",
            "category_c1e_begin" => "Категория C1e: начало действия",
            "category_d1_begin" => "Категория D1: начало действия",
            "category_c1e_end" => "Категория C1e: конец действия",
            "category_d1_end" => "Категория D1: конец действия",
            "category_d1e_begin" => "Категория D1e: начало действия",
            "category_d1e_end" => "Категория D1e: конец действия",
            "category_m_begin" => "Категория M: начало действия",
            "category_m_end" => "Категория M: конец действия",
            "date_of_expiry" => "Дата окончания срока действия",
            "sex" => "Sex",
            "fio" => "ФИО",
            "date" => "Дата выдачи",
            "inn" => "ИНН",
            "issuer_number" => "Номер налогового органа",
            "series" => "Серия",
            "day_of_birth" => "Дата рождения: день",
            "month_of_birth" => "Дата рождения: месяц",
            "year_of_birth" => "Дата рождения: год",
            "day_of_issue" => "Дата регистрации: день",
            "month_of_issue" => "Дата регистрации: месяц",
            "year_of_issue" => "Дата регистрации: год",
            "nation" => "Национальность",
            "passport_num" => "Паспорт №",
            "personal_number" => "Персональный номер",
            "patron" => "Отчество",
            "document_number" => "Серия и номер СТС",
            "barcode" => "Штрихкод, цифровой шифр",
            "nationality" => "Nationality",
            "address" => "Адрес собственника",
            "personal_id" => "Индивидуальный идентификационный номер",
            "mrz" => "MRZ",
            "date_of_marriage" => "Дата заключения брака",
            "husband_name" => "Фамилия 1 (муж)",
            "wife_name" => "Фамилия 2 (жена)",
            "from" => "Срок пребывания от",
            "statement_year" => "Год справки",
            "doc_date" => "Дата формирования справки",
            "oktmo" => "Код по ОКТМО",
            "phone" => "Телефон",
            "agent_inn" => "ИНН",
            "agent_kpp" => "КПП",
            "agent" => "Налоговый агент",
            "ru_inn" => "ИНН в Российской Федерации",
            "other_name" => "Отчество",
            "status" => "Статус налогоплательщика",
            "citizenship" => "Гражданство",
            "id_code" => "Код документа, удост. личность",
            "id_series_number" => "Серия и номер документа",
            "tax_rate" => "Ставка налога",
            "income_table" => "3. Доходы (новая модель)",
            "top_left_table_0_00_00" => "3. Доходы - левая часть таблицы (старая модель)",
            "top_right_table_0_00_00" => "3. Доходы - правая часть таблицы (старая модель)",
            "middle_0_0_0" => "4. Стандартные, социальные и имущественные налоговые вычеты (старая модель)",
            "bottom_0_0_0" => "5. Общие суммы дохода и налога (старая модель)",
            "tax_total_amount" => "Общая сумма налога",
            "tax_withheld_amount" => "Сумма налога удержанная",
            "short_name" => "Сокращенное наименование юрлица (при наличии)",
            "ogrn" => "ОГРН",
            "issuing_authority" => "Орган, выдавший документ",
            "subdivision_code" => "Код подразделения",
            "first_name" => "Имя",
            "other_names" => "Отчество",
            "series_and_number" => "Серия и номер документа",
            "region" => "Район",
            "region_district" => "Район",
            "locality" => "Пункт",
            "locality_district" => "Р-н",
            "street" => "Улица",
            "house" => "Дом",
            "building" => "Корп.",
            "apartment" => "Кв.",
            "vin" => "Номер VIN",
            "model" => "Марка, модель ТС",
            "type" => "Наименование (тип ТС)",
            "engine_number" => "Двигатель №",
            "chassis" => "Шасси (рама)",
            "cabine" => "Кузов, кабина",
            "color" => "Цвет",
            "engine_power" => "Мощность двигателя",
            "engine_volume" => "Рабочий объем двигателя",
            "engine_type" => "Тип двигателя",
            "max_mass" => "Разрешенная max масса",
            "empty_mass" => "Масса без нагрузки",
            "country" => "Страна вывоза ТС",
            "vehicle_owner" => "Наименование ФИО собственника",
            "seller_name" => "Название фирмы продавца",
            "seller_address" => "Адрес продавца",
            "seller_inn" => "ИНН продавца",
            "seller_kpp" => "КПП продавца",
            "buyer_name" => "Название фирмы покупателя",
            "buyer_address" => "Адрес покупателя",
            "buyer_kpp" => "КПП покупателя",
            "buyer_inn" => "ИНН покупателя",
            "currency_name" => "Название валюты",
            "currency_code" => "Код валюты",
            "table" => "Таблица со свойствами проданных товаров",
            "prints" => "Печати",
            "total_sum" => "Общая сумма сделки",
            "signature" => "Подписи",
            "electronic_signature" => "Электронная подпись",
            "surname_rus" => "Собственник: фамилия (на русском)",
            "surname_eng" => "Surname",
            "name_rus" => "Собственник: имя (на русском)",
            "name_eng" => "Name",
            "sex_rus" => "Пол владельца на русском языке",
            "sex_eng" => "Пол владельца транслитерацией",
            "patter" => "Отчество",
            "itn" => "ИНН",
            "accident_circumstances" => "Обстоятельства ДТП",
            "miscellaneous" => "Примечание",
            "autonomous_moving" => "Может ли ТС передвигаться своим ходом?",
            "accident_place_city" => "Место ДТП: город",
            "accident_place_address" => "Место ДТП: адрес",
            "date_of_accident" => "Дата ДТП",
            "time_of_accident" => "Время ДТП",
            "damaged_vehicles_number" => "Количество поврежденных ТС",
            "injured_people_number" => "Количество раненых",
            "dead_people_number" => "Количество погибших",
            "medical_examination" => "Проводилолсь ли мед. осв.",
            "damage_to_other_property" => "Мат. ущерб другому имуществу",
            "gibdd_report" => "Проводилось ли оформление сотр. ГИБДД",
            "a_vehicle_brand" => "А - Марка",
            "a_vehicle_model" => "А - Модель",
            "a_vin_field" => "А - VIN",
            "a_veh_state_reg_number" => "А - ГРН ТС",
            "a_veh_reg_certificate_series" => "А - СТС: серия",
            "a_veh_reg_certificate_number" => "А - СТС: номер",
            "a_owner_legal_form" => "А - Собственник ТС: ОПФ",
            "a_owner_title" => "А - Собственник ТС: наименование",
            "a_vehicle_owner_surname" => "А - Собственник ТС: Фамилия",
            "a_vehicle_owner_name" => "А - Собственник ТС: Имя",
            "a_vehicle_owner_patronym" => "А - Собственник ТС: Отчество",
            "a_address_owner" => "А - Адрес",
            "a_veh_driver_surname" => "А - Водитель ТС: Фамилия",
            "a_veh_driver_name" => "А - Водитель ТС: Имя",
            "a_veh_driver_patronym" => "А - Водитель ТС: Отчество",
            "a_date_of_birth" => "А - Дата рождения",
            "a_address_driver" => "А - Адрес",
            "a_phone" => "А - Телефон",
            "a_licence_series" => "А - ВУ: серия",
            "a_licence_number" => "А - ВУ: номер",
            "a_licence_category" => "А - ВУ: категория",
            "a_date_of_issue" => "А - ВУ: дата выдачи",
            "a_insurer" => "А - Страховщик",
            "a_policy_series" => "А - Страховой полис: серия",
            "a_policy_number" => "А - Страховой полис: номер",
            "a_first_collision_place" => "А - Место первоначального удара",
            "a_damages_scale_list" => "А - Характер и перечень поврежденных деталей и элементов",
            "b_vehicle_brand" => "B - Марка",
            "b_vehicle_model" => "B - Модель",
            "b_vin_field" => "B - VIN",
            "b_veh_state_reg_number" => "B - ГРН ТС",
            "b_veh_reg_certificate_series" => "B - СТС: серия",
            "b_veh_reg_certificate_number" => "B - СТС: номер",
            "b_owner_legal_form" => "B - Собственник ТС: ОПФ",
            "b_owner_title" => "B - Собственник ТС: наименование",
            "b_vehicle_owner_surname" => "B - Собственник ТС: Фамилия",
            "b_vehicle_owner_name" => "B - Собственник ТС: Имя",
            "b_vehicle_owner_patronym" => "B - Собственник ТС: Отчество",
            "b_address_owner" => "B - Адрес",
            "b_veh_driver_surname" => "B - Водитель ТС: Фамилия",
            "b_veh_driver_name" => "B - Водитель ТС: Имя",
            "b_veh_driver_patronym" => "B - Водитель ТС: Отчество",
            "b_date_of_birth" => "B - Дата рождения",
            "b_address_driver" => "B - Адрес",
            "b_phone" => "B - Телефон",
            "b_licence_series" => "B - ВУ: серия",
            "b_licence_number" => "B - ВУ: номер",
            "b_licence_category" => "B - ВУ: категория",
            "b_date_of_issue" => "B - ВУ: дата выдачи",
            "b_insurer" => "B - Страховщик",
            "b_policy_series" => "B - Страховой полис: серия",
            "b_policy_number" => "B - Страховой полис: номер",
            "b_first_collision_place" => "B - Место первоначального удара",
            "b_damages_scale_list" => "B - Характер и перечень поврежденных деталей и элементов",
            "checkbox_a1_veh_moving" => "Пункт 1, сторона А",
            "checkbox_a2_no_driver" => "Пункт 2, сторона А",
            "checkbox_a3_parking_moving" => "Пункт 3, сторона А",
            "checkbox_a4_driving_out" => "Пункт 4, сторона А",
            "checkbox_a5_driving_in" => "Пункт 5, сторона А",
            "checkbox_a6_driving_straight" => "Пункт 6, сторона А",
            "checkbox_a7_crossway" => "Пункт 7, сторона А",
            "checkbox_a8_into_roundabout" => "Пункт 8, сторона А",
            "checkbox_a9_roundabout" => "Пункт 9, сторона А",
            "checkbox_a10_same_line" => "Пункт 10, сторона А",
            "checkbox_a11_other_line" => "Пункт 11, сторона А",
            "checkbox_a12_change_line" => "Пункт 12, сторона А",
            "checkbox_a13_overtaking" => "Пункт 13, сторона А",
            "checkbox_a14_turning_right" => "Пункт 14, сторона А",
            "checkbox_a15_turning_left" => "Пункт 15, сторона А",
            "checkbox_a16_turning_around" => "Пункт 16, сторона А",
            "checkbox_a17_in_reverse" => "Пункт 17, сторона А",
            "checkbox_a18_oncoming_lane" => "Пункт 18, сторона А",
            "checkbox_a19_left_from_me" => "Пункт 19, сторона А",
            "checkbox_a20_no_priority_sign" => "Пункт 20, сторона А",
            "checkbox_a21_hitting" => "Пункт 21, сторона А",
            "checkbox_a22_stopped" => "Пункт 22, сторона А",
            "checkbox_a23_other" => "Пункт 23, сторона А",
            "checkbox_b1_veh_moving" => "Пункт 1, сторона Б",
            "checkbox_b2_no_driver" => "Пункт 2, сторона Б",
            "checkbox_b3_parking_moving" => "Пункт 3, сторона Б",
            "checkbox_b4_driving_out" => "Пункт 4, сторона Б",
            "checkbox_b5_driving_in" => "Пункт 5, сторона Б",
            "checkbox_b6_driving_straight" => "Пункт 6, сторона Б",
            "checkbox_b7_crossway" => "Пункт 7, сторона Б",
            "checkbox_b8_into_roundabout" => "Пункт 8, сторона Б",
            "checkbox_b9_roundabout" => "Пункт 9, сторона Б",
            "checkbox_b10_same_line" => "Пункт 10, сторона Б",
            "checkbox_b11_other_line" => "Пункт 11, сторона Б",
            "checkbox_b12_change_line" => "Пункт 12, сторона Б",
            "checkbox_b13_overtaking" => "Пункт 13, сторона Б",
            "checkbox_b14_turning_right" => "Пункт 14, сторона Б",
            "checkbox_b15_turning_left" => "Пункт 15, сторона Б",
            "checkbox_b16_turning_around" => "Пункт 16, сторона Б",
            "checkbox_b17_in_reverse" => "Пункт 17, сторона Б",
            "checkbox_b18_oncoming_lane" => "Пункт 18, сторона Б",
            "checkbox_b19_left_from_me" => "Пункт 19, сторона Б",
            "checkbox_b20_no_priority_sign" => "Пункт 20, сторона Б",
            "checkbox_b21_hitting" => "Пункт 21, сторона Б",
            "checkbox_b22_stopped" => "Пункт 22, сторона Б",
            "checkbox_b23_other" => "Пункт 23, сторона Б",
            "differences" => "Наличие разногласий",
            "no_differences" => "Отсутствие разногласий",
            "signature_1" => "Подпись №1",
            "signature_2" => "Подпись №2",
            "rntrc" => "РНОКПП. Регистрационный номер учетной карточки налогоплательщика (при наличии)",
            "surname_ukr" => "Прiзвище",
            "name_ukr" => "Iм'я",
            "third" => "По Батьковi",
            "gender" => "Пол",
            "authority" => "Орган, выдавший документ",
            "patronymic_rus" => "Собственник: отчество (на русском)",
            "city" => "Нас. пункт",
            "house_number" => "Дом",
            "building_number" => "Корпус",
            "apartment_number" => "Квартира",
            "police_unit_code" => "Орган, выдавший документ",
            "province_rus" => "Республика, край, область",
            "province" => "Республика, край, область",
            "legal_name_rus" => "Название владельца (юрлицо на русском)",
            "legal_name" => "Название владельца (юрлицо на английском)",
            "reg_number" => "Регистрационный знак",
            "brand_model_rus" => "Марка, модель (на русском)",
            "brand_model_eng" => "Марка, модель (на английском)",
            "vehicle_type" => "Тип ТС",
            "vehicle_category" => "Категория ТС",
            "release_year" => "Год выпуска ТС",
            "engine_model" => "Модель двигателя",
            "vehicle_chassis" => "Шасси №",
            "vehicle_body" => "Кузов №",
            "engine_kw" => "Мощность двигателя, кВт",
            "engine_hp" => "Мощность двигателя, л. с.",
            "ecologic_class" => "Экологический класс",
            "passport_series" => "Паспорт ТС: серия",
            "passport_number" => "Паспорт ТС: номер",
            "mass" => "Масса без нагрузки",
            "temporary_registration_term" => "Срок временной регистрации",
        ];
    }
}
