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

final class SearchIndividualsPersonModeAction
{
    private DocumentRepositoryInterface $documentRepository;

    public function __construct(DocumentRepositoryInterface $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function execute(SearchIndividualsPersonModeRequest $request): SearchIndividualsPersonModeResponse
    {
        if (
            !$request->getName()
            && !$request->getSurname()
            && !$request->getPatronymic()
            && !$request->getBirthDate()
        ) {
            throw new NoSearchQueryParamsException();
        }

        $criteria = [];

        if (
            !$request->getName()
            && !$request->getSurname()
            && !$request->getPatronymic()
            && $request->getBirthDate()
        ) {
            throw new OnlyBirthDateException();
        }

        if ($request->getName()) {
            $criteria[] = new FirstNameCriterion($request->getName());
        }

        if ($request->getSurname()) {
            $criteria[] = new SecondNameCriterion($request->getSurname());
        }

        if ($request->getPatronymic()) {
            $criteria[] = new PatronymicCriterion($request->getPatronymic());
        }

        if ($request->getBirthDate()) {
            $criteria[] = new DateBirthCriterion($request->getBirthDate());
        }

        $individuals = $this->documentRepository->findByCriteria(...$criteria)
            ->map(fn($document) => $document->individual)
            ->unique('id');

        $fio = Str::lower($request->getSurname()) . ' ' .
            Str::lower($request->getName()) . ' ' .
            Str::lower($request->getPatronymic());

        $criteria = [new FioCriterion($fio)];

        $individualsNext = $this->documentRepository->findByCriteria(...$criteria)
            ->map(fn($document) => $document->individual)
            ->unique('id');

        $individuals = $individuals->merge($individualsNext);

        return new SearchIndividualsPersonModeResponse($individuals);
    }
}
