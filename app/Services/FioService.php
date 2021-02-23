<?php

namespace App\Services;

use App\Constants\FieldTypes;
use App\Models\Individual;
use Illuminate\Support\Str;

class FioService
{
    public static function getNameFromResponse(array $response) {
        $name = null;
        foreach ($response['items'][0]['fields'] as $fieldType => $item) {
            if (in_array($fieldType, FieldTypes::getNameTypes())) {
                $name = $item['text'] ?? '';
            }
        }
        return Str::lower($name);
    }

    public static function getSurnameFromResponse(array $response) {
        $surname = null;
        foreach ($response['items'][0]['fields'] as $fieldType => $item) {
            if (in_array($fieldType, FieldTypes::getSurnameTypes())) {
                $surname = $item['text'] ?? '';
            }
        }
        return Str::lower($surname);
    }

    public static function getFioFromResponse(array $response) {
        $fio = null;
        $name = null;
        $surname = null;
        $patronymic = null;
        foreach ($response['items'][0]['fields'] as $fieldType => $item) {
            if (in_array($fieldType, FieldTypes::getFioTypes())) {
                $fio = $item['text'] ?? '';
            }
            if (in_array($fieldType, FieldTypes::getNameTypes())) $name = $item['text'];
            if (in_array($fieldType, FieldTypes::getSurnameTypes())) $surname = $item['text'];
            if (in_array($fieldType, FieldTypes::getPatronymicTypes())) $patronymic = $item['text'];
        }

        if ($name && $surname && $patronymic) {
            $fio = $surname . ' ' . $name . ' ' . $patronymic;
        }

        return Str::lower($fio);
    }

    public static function getPatronymicFromResponse(array $response) {
        $patronymic = null;
        foreach ($response['items'][0]['fields'] as $fieldType => $item) {
            if (in_array($fieldType, FieldTypes::getPatronymicTypes())) {
                $patronymic = $item['text'] ?? '';
            }
        }
        return Str::lower($patronymic);
    }

    public static function getBirthDateFromResponse(array $response) {
        $birthDate = null;
        $birthDay = null;
        $birthMonth = null;
        $birthYear = null;
        foreach ($response['items'][0]['fields'] as $fieldType => $item) {
            if (in_array($fieldType, FieldTypes::getBornFullDateTypes())) {
                $birthDate = $item['text'] ?? '';
            }
            if ($fieldType === "day_of_birth") $birthDay = $item['text'] ?? '';
            if ($fieldType === "month_of_birth") $birthMonth = $item['text'] ?? '';
            if ($fieldType === "year_of_birth") $birthYear = $item['text'] ?? '';
        }
        if ($birthDay && $birthMonth && $birthYear) {
            $birthDate = $birthDay . '.' . $birthMonth . '.' . $birthYear;
        }
        return Str::lower($birthDate);
    }

    public static function getIndividualName(Individual $individual)
    {
        $name = null;
        $docs = $individual->documents;
        foreach ($docs as $doc) {
            $fields = $doc->fields;
            foreach ($fields as $field) {
                if (in_array($field->type, FieldTypes::getNameTypes())) {
                    $name = $field->value;
                }
            }
        }
        return Str::lower($name);
    }

    public static function getIndividualSurname(Individual $individual)
    {
        $surname = null;
        $docs = $individual->documents;
        foreach ($docs as $doc) {
            $fields = $doc->fields;
            foreach ($fields as $field) {
                if (in_array($field->type, FieldTypes::getSurnameTypes())) {
                    $surname = $field->value;
                }
            }
        }
        return Str::lower($surname);
    }

    public static function getIndividualPatronymic(Individual $individual)
    {
        $patronymic = null;
        $docs = $individual->documents;
        foreach ($docs as $doc) {
            $fields = $doc->fields;
            foreach ($fields as $field) {
                if (in_array($field->type, FieldTypes::getPatronymicTypes())) {
                    $patronymic = $field->value;
                }
            }
        }
        return Str::lower($patronymic);
    }

    public static function getIndividualBirthDate(Individual $individual)
    {
        $birthDate = null;
        $birthDay = null;
        $birthMonth = null;
        $birthYear = null;
        $docs = $individual->documents;
        foreach ($docs as $doc) {
            $fields = $doc->fields;
            foreach ($fields as $field) {
                if (in_array($field->type, FieldTypes::getBornFullDateTypes())) {
                    $birthDate = $field->value;
                }
                if ($field->type === "day_of_birth") $birthDay = $field->value;
                if ($field->type === "month_of_birth") $birthMonth = $field->value;
                if ($field->type === "year_of_birth") $birthYear = $field->value;
            }
            if ($birthDay && $birthMonth && $birthYear) {
                $birthDate = $birthDay . '.' . $birthMonth . '.' . $birthYear;
            }
        }
        return Str::lower($birthDate);
    }

    public static function getIndividualFio(Individual $individual)
    {
        $fio = null;
        $birthDay = null;
        $birthMonth = null;
        $birthYear = null;
        $docs = $individual->documents;
        foreach ($docs as $doc) {
            $fields = $doc->fields;
            foreach ($fields as $field) {
                if (in_array($field->type, FieldTypes::getFioTypes())) {
                    $fio = $field->value;
                }
                if (in_array($field->type, FieldTypes::getNameTypes())) $name = $field->value;
                if (in_array($field->type, FieldTypes::getSurnameTypes())) $surname = $field->value;
                if (in_array($field->type, FieldTypes::getPatronymicTypes())) $patronymic = $field->value;
            }

            if ($name && $surname && $patronymic) {
                $fio = $surname . ' ' . $name . ' ' . $patronymic;
            }
        }
        return Str::lower($fio);
    }
}
