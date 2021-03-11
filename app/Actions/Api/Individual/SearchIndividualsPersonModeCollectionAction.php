<?php

namespace App\Actions\Api\Individual;

use App\Exceptions\Api\NoSearchQueryParamsException;
use App\Exceptions\Api\OnlyBirthDateException;
use App\Repositories\Document\Criterion\DateBirthCriterion;
use App\Repositories\Document\Criterion\FioCriterion;
use App\Repositories\Document\Criterion\FirstNameCriterion;
use App\Repositories\Document\Criterion\PatronymicCriterion;
use App\Repositories\Document\Criterion\SecondNameCriterion;
use App\Repositories\Document\DocumentRepositoryInterface;
use Illuminate\Support\Str;

final class SearchIndividualsPersonModeCollectionAction
{
    private DocumentRepositoryInterface $documentRepository;

    public function __construct(DocumentRepositoryInterface $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function execute(
        SearchIndividualsPersonModeCollectionRequest $request
    ): SearchIndividualsPersonModeCollectionResponse {
        $response = [];
        foreach ($request->getIndividuals() as $individual) {
            $name = $individual['name'] ?? null;
            $surname ??= $individual['surname'] ?? null;
            $patronymic ??= $individual['patronymic'] ?? null;
            $dateBirth ??= $individual['date_birth'] ?? null;

            if (
                !$name
                && !$surname
                && !$patronymic
                && !$dateBirth
            ) {
                throw new NoSearchQueryParamsException();
            }

            $criteria = [];

            if (
                !$name
                && !$surname
                && !$patronymic
                && $dateBirth
            ) {
                throw new OnlyBirthDateException();
            }

            if ($name) {
                $criteria[] = new FirstNameCriterion($name);
            }

            if ($patronymic) {
                $criteria[] = new SecondNameCriterion($surname);
            }

            if ($patronymic) {
                $criteria[] = new PatronymicCriterion($patronymic);
            }

            if ($dateBirth) {
                $criteria[] = new DateBirthCriterion($dateBirth);
            }

            $individuals = $this->documentRepository->findByCriteria(...$criteria)
                ->map(fn($document) => $document->individual)
                ->unique('id');

            $fio = Str::lower($name) . ' ' .
                Str::lower($surname) . ' ' .
                Str::lower($patronymic);

            $criteria = [new FioCriterion($fio)];

            $individualsNext = $this->documentRepository->findByCriteria(...$criteria)
                ->map(fn($document) => $document->individual)
                ->unique('id');

            $response[] = $individuals->merge($individualsNext)->all()[0];
        }

        return new SearchIndividualsPersonModeCollectionResponse(collect($response));
    }
}
