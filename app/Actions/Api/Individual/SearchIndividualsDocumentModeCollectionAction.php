<?php

namespace App\Actions\Api\Individual;

use App\Exceptions\Api\NoSearchQueryParamsException;
use App\Repositories\Document\Criterion\InnNumberCriterion;
use App\Repositories\Document\Criterion\PassportNumberCriterion;
use App\Repositories\Document\Criterion\SnilsNumberCriterion;
use App\Repositories\Document\DocumentRepositoryInterface;

final class SearchIndividualsDocumentModeCollectionAction
{
    private DocumentRepositoryInterface $documentRepository;

    public function __construct(DocumentRepositoryInterface $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function execute(
        SearchIndividualsDocumentModeCollectionRequest $request
    ): SearchIndividualsDocumentModeCollectionResponse {
        $response = [];
        foreach ($request->getIndividuals() as $individual) {
            $innNumber = $individual['inn_number'] ?? null;
            $snilsNumber = $individual['snils_number'] ?? null;
            $passportNumber = $individual['passport_number'] ?? null;

            if (
                !$innNumber
                && !$snilsNumber
                && !$passportNumber
            ) {
                throw new NoSearchQueryParamsException();
            }

            $criteria = [];

            if ($innNumber) {
                $criteria[] = new InnNumberCriterion($innNumber);
            }

            if ($snilsNumber) {
                $criteria[] = new SnilsNumberCriterion($snilsNumber);
            }

            if ($passportNumber) {
                $criteria[] = new PassportNumberCriterion($passportNumber);
            }

            $response[] = $this->documentRepository->findByCriteria(...$criteria)
                ->map(fn($document) => $document->individual)
                ->unique('id')->all()[0];
        }

        return new SearchIndividualsDocumentModeCollectionResponse(collect($response));
    }
}
