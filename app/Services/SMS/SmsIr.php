<?php


namespace App\Services\SMS;


use App\Models\SmsConfig;
use Illuminate\Support\Facades\Http;

class SmsIr
{
    private $apiKey;

    private $secretKey;

    private $token;
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private $baseUrl;
    private $senderNumber;

    public function __construct()
    {
        $this->baseUrl = config('sms.api-base-url');
        $this->apiKey = config('sms.api-key');
        $this->secretKey = config('sms.secret-key');
        $this->senderNumber = config('sms.sender-number');

        $smsConfig = SmsConfig::find(1);

        if ($smsConfig) {
            $this->baseUrl = $smsConfig->api_url;
            $this->apiKey = $smsConfig->api_key;
            $this->secretKey = $smsConfig->api_secret;
            $this->senderNumber = $smsConfig->api_number;
        }
    }

    public function send($message, $number) {
        if ($this->getToken()) {
            $response = Http::withHeaders([
                'x-sms-ir-secure-token' => $this->token
            ])
                ->timeout(10)
                ->post($this->baseUrl . 'MessageSend', [
                    "Messages"=>[$message],
                    "MobileNumbers"=> [$number],
                    "LineNumber"=> $this->senderNumber,
                    "SendDateTime"=> "",
                    "CanContinueInCaseOfError"=> "false",
                ]);

            return $response;
        }

        return false;
    }

    public function sendOtpCode($code, $mobile) {
        if ($this->getToken()) {
            return Http::withHeaders([
                'x-sms-ir-secure-token' => $this->token
            ])
            ->timeout(10)
            ->post($this->baseUrl . 'UltraFastSend', [
                "ParameterArray" => [
                    [
                        "Parameter" => "VerificationCode",
                        "ParameterValue" => $code
                    ]
                ],
                "Mobile" => $mobile,
                "TemplateId" => 10237
            ]);
        }

        return false;
    }

    private function getToken() {
        $response = Http::post($this->baseUrl . 'Token', [
            "UserApiKey" => $this->apiKey,
	        "SecretKey" => $this->secretKey
        ]);

        if ($response->successful()) {
            if ($response->json()['IsSuccessful']) {
                $this->token = $response->json()['TokenKey'];
                return true;
            }
        }
        return false;
    }
}
