<?php

namespace App\Actions\Individual;

use App\Repositories\Individual\Criterion\FioCriterion;
use App\Repositories\Individual\Criterion\InnNumberCriterion;
use App\Repositories\Individual\Criterion\NameCriterion;
use App\Repositories\Individual\Criterion\PassportNumberCriterion;
use App\Repositories\Individual\Criterion\PatronymicCriterion;
use App\Repositories\Individual\Criterion\SnilsNumberCriterion;
use App\Repositories\Individual\Criterion\SurnameCriterion;
use App\Repositories\Individual\IndividualRepository;

class SearchIndividualsAction
{
    private IndividualRepository $repository;

    public function __construct(IndividualRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(SearchIndividualsRequest $request)
    {
        $criteria = [];
        if ($request->getSnilsNumber()) {
            $criteria[] = new SnilsNumberCriterion($request->getSnilsNumber());
        }

        if ($request->getPassportNumber()) {
            $criteria[] = new PassportNumberCriterion($request->getPassportNumber());
        }

        if ($request->getInnNumber()) {
            $criteria[] = new InnNumberCriterion($request->getInnNumber());
        }

        if ($request->getName()) {
            $criteria[] = new NameCriterion($request->getName());
        }

        if ($request->getSurname()) {
            $criteria[] = new SurnameCriterion($request->getSurname());
        }

        if ($request->getPatronymic()) {
            $criteria = new PatronymicCriterion($request->getPatronymic());
        }

        if ($request->getName() && $request->getSurname() && $request->getPatronymic()) {
            $criteria[] = new FioCriterion($request->getName(), $request->getSurname(), $request->getPatronymic());
        }

        return new SearchIndividualsResponse(
            $this->repository->findByDocumentCriteria($criteria)
        );
    }
}
