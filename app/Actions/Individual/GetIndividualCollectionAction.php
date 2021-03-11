<?php

namespace App\Actions\Individual;

use App\Actions\PaginatedResponse;
use App\Repositories\Individual\Criterion\HasDocumentsCriterion;
use App\Repositories\Individual\Criterion\OrderByCriterion;
use App\Repositories\Individual\IndividualRepository;
use App\Repositories\Individual\IndividualRepositoryInterface;

final class GetIndividualCollectionAction
{
    private IndividualRepositoryInterface $individualRepository;

    public function __construct(IndividualRepositoryInterface $individualRepository)
    {
        $this->individualRepository = $individualRepository;
    }

    public function execute(GetIndividualCollectionRequest $request): PaginatedResponse
    {
        $criteria = [
            new HasDocumentsCriterion()
        ];

        $individuals = $this->individualRepository->paginatedByCriteria(
            $criteria,
            $request->getPage(),
            $request->getPerPage(),
            $request->getSorting() ?? IndividualRepository::DEFAULT_SORTING,
            $request->getDirection() ?? IndividualRepository::DEFAULT_DIRECTION
        );

        return new PaginatedResponse($individuals);
    }
}
