<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Leads;
use Carbon\Carbon;
use League\OAuth2\Client\Token\AccessTokenInterface;


use AmoCRM\Models\LeadModel;
use App\Helpers\AmoCRM;

class SyncLeadsFromAmoCRM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrm:sync-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запускает процесс сихронизации сделок';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $client_id = config('services.amocrm.key');
        $base_domain = config('services.amocrm.base_domain');
        $client_secret = config('services.amocrm.secret');
        $redirect_uri = config('services.amocrm.redirect_uri');
        $authorization_code = config('services.amocrm.authorization_code');

        if(!$client_id || !$client_secret || !$authorization_code || !$base_domain) {
            $this->error('Сначала необходимо настроить интеграцию с AmoCRM в .env');
            return;
        } 

        $apiClient = new \AmoCRM\Client\AmoCRMApiClient($client_id, $client_secret, $redirect_uri);
        $apiClient->getOAuthClient()->setBaseDomain($base_domain);

        $amoCRM = new AmoCRM;
        $accessToken = $amoCRM->getToken();
        if(!$accessToken) {
            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($authorization_code);
            $amoCRM->saveToken(
                [
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $base_domain,
                ]
            );
        }

        $apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($base_domain)
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $base_domain) {
                    $amoCRM->saveToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $base_domain,
                        ]
                    );
                }
            );
        
        $leadsService = $apiClient->leads();
        $leadsCollection = $leadsService->get();
        $this->saveData($leadsCollection, $apiClient);
    }

    public function saveData($leadsCollection, $apiClient) {
        foreach($leadsCollection as $leadC) {
            $lead = $apiClient->leads()->getOne($leadC->id, [LeadModel::CONTACTS]);
            $contact = $apiClient->contacts()->getOne($lead->contacts[0]->id);
            $customFields = $contact->getCustomFieldsValues();
            $phoneField = $customFields->getBy('fieldCode', 'PHONE');
            $emailField = $customFields->getBy('fieldCode', 'EMAIL');

            Leads::firstOrCreate(
                ["amocrm_id" => $lead->id],
                [
                    "name" => $lead->name,
                    "price" => $lead->price,
                    "is_deleted" => $lead->isDeleted,
                    "closed_at" => $lead->closedAt,
                    "contact_name" => $contact->name,
                    "contact_phone" => $phoneField?->values[0]?->value ? $phoneField->values[0]->value : '',
                    "contact_email" => $emailField?->values[0]?->value ? $emailField->values[0]->value : '',
                ]
            );
            

            $this->info("Сохранен лид #" . $lead->id);
        }

        if($leadsCollection->getNextPageLink()) {
            $leadsCollection = $apiClient->leads()->nextPage($leadsCollection);
            $this->saveData($leadsCollection, $apiClient);
        }
    }
}
