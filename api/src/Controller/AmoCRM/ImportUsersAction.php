<?php

declare(strict_types=1);

namespace App\Controller\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiNoContentException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\Leads\Pipelines\Statuses\StatusModel;
use AmoCRM\Models\TagModel;
use App\Dto\Amo\Employee;
use App\Dto\Amo\Lead;
use App\Entity\User\User;
use App\Flusher;
use App\Repository\AmoCrmTokenRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amo-crm/import-users', name: 'amo-crm_import-users', methods: ["GET"])]
class ImportUsersAction extends AbstractController
{

    private AmoCRMApiClient $client;
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        AmoCrmTokenRepository $tokens,
        Flusher $flusher

    )
    {
        $apiClient = new AmoCRMApiClient(
            'd80b0f1f-1687-4b1e-8abd-9f3cbbe7a19e',
            'fCUzh7hiQ1bcuKQSrdJVp7Mnwnwi4b2vsK4W7yzhBCcumEkvRcHl3wX3hVxglhmK',
            'https://ambulance.rc-respect.ru/api/amo-crm/auth/callback'
        );

        $token = $tokens->getToken();

        $apiClient->setAccessToken($token)
            ->setAccountBaseDomain('af4040148.amocrm.ru')
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) use ($tokens) {
                    $tokens->update($accessToken, $baseDomain);
                }
            );

        $this->client = $apiClient;
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function __invoke(Request $request): JsonResponse
    {

        /** @var Employee[] $employees */
        $employees = $this->getUsers();
        foreach ($employees as $employee){

            $count = $this->users->getCountUsers();

            $user = $this->users->findOneByExternalId($employee->getId());
            if ($user){
                $user->setRoles([$employee->getRole()]);
                $user->setName($employee->getName());
                $this->flusher->flush();
            }else{
                $user = new User($employee->getId());
                $user->setRoles([$employee->getRole()]);
                $user->setPosition('');
                $user->setName($employee->getName());
                $user->setPhone('7900000' . sprintf('%04d', $count + 1));
                $user->setPassword('$2y$13$JlPNBmX5LMm.2SRisCsJ1u3QmwHOmQhRSesxSgcEVMlh/OfOcOX0G');
                $this->users->save($user, true);
            }
        }

        return $this->json([], Response::HTTP_OK);
    }

    private function getUsers(): array
    {
        $filter = new LeadsFilter();
        $filter->setPipelineIds([4105087]);
        $leadsService = $this->client->leads();
        $leads = $leadsService->get($filter);
        $employees = [];
        foreach ($leads as $lead) {
            $employee = $this->getAmoUser($lead);
            if ($employee){
                $employees[] = $employee;
            }
        }
        while ($leads->getNextPageLink()) {
            $leads = $leadsService->nextPage($leads);
            foreach ($leads as $lead) {
               $employee = $this->getAmoUser($lead);
                if ($employee){
                    $employees[] = $employee;
                }
            }
        }

       return $employees;
    }

    private function getAmoUser(LeadModel $lead): ?Employee
    {
        $badStatuses = [142, 143];
        if (in_array($lead->getStatusId(), $badStatuses, true)) {
            return null;
        }
        if (!$lead->getTags()) {
            return null;
        }

        $role = null;
        /** @var TagModel $tag */
        foreach ($lead->getTags() as $tag) {
            if ($tag->getId() === 62145){
                $role = 'ROLE_ADMIN';
            }
            if ($tag->getId() === 62135){
                $role = 'ROLE_DOCTOR';
            }
        }

        return new Employee($lead->getId(), $lead->getName(), $role);
    }
}
