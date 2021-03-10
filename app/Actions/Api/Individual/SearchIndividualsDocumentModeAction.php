<?php

namespace App\Actions\Api\Individual;

use App\Exceptions\Api\NoSearchQueryParamsException;
use App\Repositories\Document\Criterion\InnNumberCriterion;
use App\Repositories\Document\Criterion\PassportNumberCriterion;
use App\Repositories\Document\Criterion\SnilsNumberCriterion;
use App\Repositories\Document\DocumentRepositoryInterface;

final class SearchIndividualsDocumentModeAction
{
    private DocumentRepositoryInterface $documentRepository;

    public function __construct(DocumentRepositoryInterface $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function execute(SearchIndividualsDocumentModeRequest $request): SearchIndividualsDocumentModeResponse
    {
        if (
            !$request->getInnNumber()
            && !$request->getSnilsNumber()
            && !$request->getPassport()
        ) {
            throw new NoSearchQueryParamsException();
        }

        $criteria = [];

        if ($request->getInnNumber()) {
            $criteria[] = new InnNumberCriterion($request->getInnNumber());
        }

        if ($request->getSnilsNumber()) {
            $criteria[] = new SnilsNumberCriterion($request->getSnilsNumber());
        }

        if ($request->getPassport()) {
            $criteria[] = new PassportNumberCriterion($request->getPassport());
        }

        $individuals = $this->documentRepository->findByCriteria(...$criteria)
            ->map(fn($document) => $document->individual)
            ->unique('id');

        return new SearchIndividualsDocumentModeResponse($individuals);
    }
}
